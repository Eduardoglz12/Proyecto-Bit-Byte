<?php
session_start();
require 'db_conexion.php';

// Seguridad: Verificar que el usuario esté en el flujo correcto y los datos lleguen por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['datos_cliente'])) {
    header('Location: ../index.php');
    exit();
}

// 1. Recoger los datos de la tarjeta
$numero_tarjeta = preg_replace('/\s+/', '', $_POST['numero_tarjeta'] ?? '');
$mes_vencimiento = $_POST['mes_vencimiento'] ?? '';
$ano_vencimiento = $_POST['ano_vencimiento'] ?? '';
$cvv = $_POST['cvv'] ?? '';

// 2. Realizar las validaciones de la tarjeta (simulación)
$error = null;
if (!ctype_digit($numero_tarjeta) || strlen($numero_tarjeta) !== 16) {
    $error = "El número de tarjeta debe contener 16 dígitos.";
} elseif (!ctype_digit($cvv) || (strlen($cvv) < 3 || strlen($cvv) > 4)) {
    $error = "El CVV debe contener 3 o 4 dígitos.";
} else {
    // Validar fecha de vencimiento
    $fecha_actual = new DateTime();
    $fecha_vencimiento = new DateTime($ano_vencimiento . '-' . $mes_vencimiento . '-01');
    $fecha_vencimiento->modify('last day of this month'); // Ir al último día del mes
    if ($fecha_vencimiento < $fecha_actual) {
        $error = "La tarjeta ha expirado.";
    }
}

// 3. Si hay un error, regresar a la página de pago
if ($error) {
    $_SESSION['error_tarjeta'] = $error;
    header('Location: ../html/seleccionar_pago.php');
    exit();
}

// 4. SI LA VALIDACIÓN ES EXITOSA, SIMULAMOS EL PAGO Y GUARDAMOS LA ORDEN
// (Este bloque es una copia de la lógica de procesar_pedido.php)
try {
    $conexion->begin_transaction();
    
    $datos_cliente = $_SESSION['datos_cliente'];
    
    // Si el usuario está logueado, actualizamos su perfil
    if (isset($_SESSION['usr_id'])) {
        // ... (código para actualizar perfil de usuario) ...
    }

    // Preparar y guardar la orden
    $direccion_completa = $datos_cliente['calle'] . ", " . $datos_cliente['colonia'] . ", " . $datos_cliente['ciudad'] . ", " . $datos_cliente['estado'] . ", C.P. " . $datos_cliente['cp'];
    $sql_order = "INSERT INTO orders (ord_date, os_id, usr_id, ord_customer_name, ord_customer_email, ord_customer_phone, ord_shipping_address) 
                  VALUES (NOW(), ?, ?, ?, ?, ?, ?)";
    $stmt_order = $conexion->prepare($sql_order);
    $usr_id_orden = $_SESSION['usr_id'] ?? null;
    $os_id = 1; // 1 = Completado
    $stmt_order->bind_param("iissss", $os_id, $usr_id_orden, $datos_cliente['nombre'], $datos_cliente['email'], $datos_cliente['telefono'], $direccion_completa);
    $stmt_order->execute();
    $new_ord_id = $conexion->insert_id;
    $stmt_order->close();

    // Guardar detalles del pedido y actualizar stock (esta lógica ya la tienes)
    // ...

    $conexion->commit();
    unset($_SESSION['carrito'], $_SESSION['datos_cliente']);
    $_SESSION['last_order_id'] = $new_ord_id;

    // Redirigir a la página de éxito
    header('Location: ../html/gracias.php?status=success');
    exit();

} catch (Exception $e) {
    $conexion->rollback();
    $_SESSION['error_tarjeta'] = "Error al procesar el pedido: " . $e->getMessage();
    header('Location: ../html/seleccionar_pago.php');
    exit();
}
?>