<?php
// php/procesar_pedido.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

// --- DEPURACIÓN ---
$log_file = __DIR__ . '/debug_log.txt';
file_put_contents($log_file, "INICIO DE DEPURACIÓN DE PEDIDO - " . date('Y-m-d H:i:s') . "\n\n");

session_start();
require 'db_conexion.php';

// --- CREDENCIALES PAYPAL SANDBOX ---
$clientID = "ASUajecFhJzfxHxdX4POf20OweQ_rqAY2zMB02SPs1Sq6EJ9loM2upMo5YcQW8GEw3_UMfWes7_I7yao";
$secret   = "ELsm95H5C9MbVibXh6zlG4mVCjk8RZqVdxEfzCM7B0N0MOYvyAlR4NlOVYYTgI9lcywdpH-jEb031idJ";
$paypalAPI = "https://api-m.sandbox.paypal.com";

// Los tokens 'token' y 'PayerID' son los nuevos parámetros que PayPal envía en la URL
$orderID = $_GET['token'] ?? null; 
$payerID = $_GET['PayerID'] ?? null;

file_put_contents($log_file, "1. Order ID (token) recibido: " . ($orderID ?? 'NINGUNO') . ", PayerID: " . ($payerID ?? 'NINGUNO') . "\n\n", FILE_APPEND);

if (!$orderID || !$payerID || empty($_SESSION['carrito'])) {
    file_put_contents($log_file, "ERROR: Faltan parámetros o el carrito está vacío.\n", FILE_APPEND);
    die("Error: Faltan parámetros para procesar el pedido.");
}

// --- 2. OBTENER TOKEN DE ACCESO ---
// (Esta parte es idéntica a tu código original)
$ch_token = curl_init();
curl_setopt($ch_token, CURLOPT_URL, "$paypalAPI/v1/oauth2/token");
curl_setopt($ch_token, CURLOPT_USERPWD, "$clientID:$secret");
curl_setopt($ch_token, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
curl_setopt($ch_token, CURLOPT_RETURNTRANSFER, true);
$response_token = curl_exec($ch_token);
curl_close($ch_token);
$data_token = json_decode($response_token);
if (!isset($data_token->access_token)) {
    die("Fallo de autenticación con PayPal.");
}
$accessToken = $data_token->access_token;
file_put_contents($log_file, "2. Token de acceso obtenido.\n\n", FILE_APPEND);

// --- 3. CAPTURAR EL PAGO (NUEVO PASO CRÍTICO) ---
$ch_capture = curl_init();
curl_setopt($ch_capture, CURLOPT_URL, "$paypalAPI/v2/checkout/orders/$orderID/capture");
curl_setopt($ch_capture, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $accessToken",
    "Content-Type: application/json"
]);
curl_setopt($ch_capture, CURLOPT_POST, true);
curl_setopt($ch_capture, CURLOPT_RETURNTRANSFER, true);

$response_capture = curl_exec($ch_capture);
curl_close($ch_capture);

$captureDetails = json_decode($response_capture);
file_put_contents($log_file, "3. Respuesta de Captura de PayPal:\n" . ($response_capture ?? 'VACÍA') . "\n\n", FILE_APPEND);

// Verificar que la captura fue exitosa y el estado es COMPLETADO
if (!$captureDetails || !isset($captureDetails->status) || $captureDetails->status !== 'COMPLETED') {
    $_SESSION['error_compra'] = "Pago no completado. Estado: " . ($captureDetails->status ?? 'N/A');
    file_put_contents($log_file, "ERROR: El estado del pago no es COMPLETED.\n", FILE_APPEND);
    header('Location: ../html/gracias.php?status=error');
    exit();
}

file_put_contents($log_file, "4. Pago COMPLETADO. Procediendo a guardar en BDD.\n", FILE_APPEND);

// --- 4. GUARDAR PEDIDO EN BASE DE DATOS ---
$conexion->begin_transaction();
try {
    // Obtenemos los datos del cliente y del carrito de la sesión
    $datos_cliente = $_SESSION['datos_cliente'];
    $carrito = $_SESSION['carrito'];
    
    // Si el usuario ESTÁ logueado, actualizamos su perfil con los datos de la compra
    if (isset($_SESSION['usr_id'])) {
        $usr_id = $_SESSION['usr_id'];
        $sql_update_user = "UPDATE users SET 
                                usr_nombre_completo = ?, usr_email = ?, usr_telefono = ?, 
                                usr_calle = ?, usr_colonia = ?, usr_ciudad = ?, 
                                usr_estado = ?, usr_cp = ?
                            WHERE usr_id = ?";
        $stmt_update = $conexion->prepare($sql_update_user);
        $stmt_update->bind_param("ssssssssi", 
            $datos_cliente['nombre'], $datos_cliente['email'], $datos_cliente['telefono'],
            $datos_cliente['calle'], $datos_cliente['colonia'], $datos_cliente['ciudad'],
            $datos_cliente['estado'], $datos_cliente['cp'], $usr_id
        );
        $stmt_update->execute();
        $stmt_update->close();
    }
    
    // --- LÓGICA MEJORADA PARA INSERTAR LA ORDEN ---
    
    // 1. Preparamos la dirección completa para guardarla
    $direccion_completa = $datos_cliente['calle'] . ", " . $datos_cliente['colonia'] . ", " . 
                          $datos_cliente['ciudad'] . ", " . $datos_cliente['estado'] . ", C.P. " . $datos_cliente['cp'];

    // 2. Preparamos la consulta para insertar la orden
    $sql_order = "INSERT INTO orders (ord_date, os_id, usr_id, ord_customer_name, ord_customer_email, ord_shipping_address) 
                  VALUES (NOW(), ?, ?, ?, ?, ?)";
    
    $stmt_order = $conexion->prepare($sql_order);

    // Si el usuario está logueado, usamos su ID. Si no, usamos NULL.
    $usr_id_orden = $_SESSION['usr_id'] ?? null;
    $os_id = 1; // Suponiendo que 1 es 'Completado'

    // 3. Vinculamos los parámetros y ejecutamos
    $stmt_order->bind_param("iisss", 
        $os_id, 
        $usr_id_orden, 
        $datos_cliente['nombre'], 
        $datos_cliente['email'], 
        $direccion_completa
    );
    $stmt_order->execute();
    $new_ord_id = $conexion->insert_id;
    $stmt_order->close();

    // 4c. Insertar detalles y actualizar stock
    $stmt_details = $conexion->prepare("INSERT INTO order_details (od_amount, prod_id, ord_id) VALUES (?, ?, ?)");
    $stmt_update_stock = $conexion->prepare("UPDATE products SET prod_stock = prod_stock - ? WHERE prod_id = ?");

    foreach ($carrito as $prod_id => $cantidad) {
        $stmt_details->bind_param("iii", $cantidad, $prod_id, $new_ord_id);
        $stmt_details->execute();

        $stmt_update_stock->bind_param("ii", $cantidad, $prod_id);
        $stmt_update_stock->execute();
    }

    $conexion->commit();
    unset($_SESSION['carrito']);
    unset($_SESSION['datos_cliente']);
    $_SESSION['last_order_id'] = $new_ord_id;

    file_put_contents($log_file, "5. ÉXITO: Pedido guardado correctamente.\n", FILE_APPEND);
    header('Location: ../html/gracias.php?status=success');

} catch (Exception $e) {
    $conexion->rollback();
    $_SESSION['error_compra'] = "Error al procesar el pedido: " . $e->getMessage();
    file_put_contents($log_file, "ERROR BDD: " . $e->getMessage() . "\n", FILE_APPEND);
    header('Location: ../html/gracias.php?status=error');
}

exit();
?>