<?php
// php/procesar_pedido.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

// --- DEPURACIÓN ---
$log_file = __DIR__ . '/debug_log.txt';
file_put_contents($log_file, "INICIO DE DEPURACIÓN DE PEDIDO - " . date('Y-m-d H:i:s') . "\n\n");

session_start();
require_once __DIR__ . '/../db_conexion.php';

// --- CREDENCIALES PAYPAL SANDBOX ---
$clientID = "ASUajecFhJzfxHxdX4POf20OweQ_rqAY2zMB02SPs1Sq6EJ9loM2upMo5YcQW8GEw3_UMfWes7_I7yao";
$secret   = "ELsm95H5C9MbVibXh6zlG4mVCjk8RZqVdxEfzCM7B0N0MOYvyAlR4NlOVYYTgI9lcywdpH-jEb031idJ";
$paypalAPI = "https://api-m.sandbox.paypal.com";

$orderID = $_GET['orderID'] ?? null;
file_put_contents($log_file, "1. Order ID recibido de la URL: " . ($orderID ?? 'NINGUNO') . "\n\n", FILE_APPEND);

if (!$orderID || empty($_SESSION['carrito'])) {
    file_put_contents($log_file, "ERROR: Order ID o carrito vacíos.\n", FILE_APPEND);
    die("Error: Order ID o carrito vacío.");
}

// --- 2. OBTENER TOKEN DE ACCESO ---
$ch_token = curl_init();
curl_setopt($ch_token, CURLOPT_URL, "$paypalAPI/v1/oauth2/token");
curl_setopt($ch_token, CURLOPT_USERPWD, "$clientID:$secret");
curl_setopt($ch_token, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
curl_setopt($ch_token, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Accept-Language: en_US']);
curl_setopt($ch_token, CURLOPT_RETURNTRANSFER, true);
$response_token = curl_exec($ch_token);

if (curl_errno($ch_token)) {
    file_put_contents($log_file, "ERROR cURL Token: " . curl_error($ch_token) . "\n", FILE_APPEND);
    die('Error de cURL al obtener token: ' . curl_error($ch_token));
}
curl_close($ch_token);

$data_token = json_decode($response_token);
if (!isset($data_token->access_token)) {
    file_put_contents($log_file, "ERROR: No se encontró access_token en la respuesta.\n", FILE_APPEND);
    $_SESSION['error_compra'] = "Fallo de autenticación con PayPal.";
    header('Location: ../gracias.php?status=error');
    exit();
}
$accessToken = $data_token->access_token;
file_put_contents($log_file, "2. Token de acceso obtenido con éxito.\n\n", FILE_APPEND);

// --- 3. VERIFICAR DETALLES DE LA ORDEN ---
$ch_order = curl_init();
curl_setopt($ch_order, CURLOPT_URL, "$paypalAPI/v2/checkout/orders/$orderID");
curl_setopt($ch_order, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $accessToken",
    "Content-Type: application/json"
]);
curl_setopt($ch_order, CURLOPT_RETURNTRANSFER, true);
$response_order = curl_exec($ch_order);

if (curl_errno($ch_order)) {
    file_put_contents($log_file, "ERROR cURL Orden: " . curl_error($ch_order) . "\n", FILE_APPEND);
    die('Error de cURL al verificar la orden: ' . curl_error($ch_order));
}
curl_close($ch_order);

$orderDetails = json_decode($response_order);
file_put_contents($log_file, "3. Respuesta de PayPal (Detalles de la Orden):\n" . ($response_order ?? 'VACÍA') . "\n\n", FILE_APPEND);

if (!$orderDetails || !in_array($orderDetails->status ?? '', ['COMPLETED', 'APPROVED'])) {
    $_SESSION['error_compra'] = "Pago no completado. Estado: " . ($orderDetails->status ?? 'N/A');
    file_put_contents($log_file, "ERROR: Estado de pago no válido.\n", FILE_APPEND);
    header('Location: ../gracias.php?status=error');
    exit();
}

file_put_contents($log_file, "4. Pago COMPLETADO. Procediendo a guardar en BDD.\n", FILE_APPEND);

// --- 4. GUARDAR PEDIDO EN BASE DE DATOS ---
$conexion->begin_transaction();
try {
    $carrito = $_SESSION['carrito'];
    $usr_id  = $_SESSION['usr_id'] ?? null;

    $product_ids = array_keys($carrito);
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));

    // 4a. Verificar stock
    $stmt_stock = $conexion->prepare("SELECT prod_id, prod_stock FROM products WHERE prod_id IN ($placeholders) FOR UPDATE");
    $stmt_stock->bind_param(str_repeat('i', count($product_ids)), ...$product_ids);
    $stmt_stock->execute();
    $res_stock = $stmt_stock->get_result();

    $productos_db = [];
    $stock_suficiente = true;
    while ($fila = $res_stock->fetch_assoc()) {
        $productos_db[$fila['prod_id']] = $fila;
    }

    foreach ($carrito as $prod_id => $cantidad) {
        if (!isset($productos_db[$prod_id]) || $productos_db[$prod_id]['prod_stock'] < $cantidad) {
            $stock_suficiente = false;
            break;
        }
    }

    if (!$stock_suficiente) throw new Exception("Stock insuficiente.");

    // 4b. Insertar orden
    $os_id = 1;
    $stmt_order = $conexion->prepare("INSERT INTO orders (ord_date, os_id, usr_id) VALUES (NOW(), ?, ?)");
    $stmt_order->bind_param("ii", $os_id, $usr_id);
    $stmt_order->execute();
    $new_ord_id = $conexion->insert_id;

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
    $_SESSION['last_order_id'] = $new_ord_id;

    file_put_contents($log_file, "5. ÉXITO: Pedido guardado correctamente.\n", FILE_APPEND);
    header('Location: ../gracias.php?status=success');

} catch (Exception $e) {
    $conexion->rollback();
    $_SESSION['error_compra'] = "Error al procesar el pedido: " . $e->getMessage();
    file_put_contents($log_file, "ERROR BDD: " . $e->getMessage() . "\n", FILE_APPEND);
    header('Location: ../gracias.php?status=error');
}

exit();
?>
