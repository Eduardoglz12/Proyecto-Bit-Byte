<?php
session_start();
  require '../php/db_conexion.php';

//Seguridad: verificar login y que se haya pasado un ID
if (!isset($_SESSION['usr_id']) || !isset($_GET['id'])) {
    header('Location: ../index.php');
    exit();
}

$usr_id = $_SESSION['usr_id'];
$ord_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$ord_id) {
    die("ID de pedido no válido.");
}

//Obtener detalles del pedido, asegurándose de que pertenece al usuario logueado
$sql_order = "SELECT ord_id, ord_date, os_name FROM orders 
              JOIN order_status ON orders.os_id = order_status.os_id
              WHERE ord_id = ? AND usr_id = ?";
$stmt_order = $conexion->prepare($sql_order);
$stmt_order->bind_param("ii", $ord_id, $usr_id);
$stmt_order->execute();
$pedido = $stmt_order->get_result()->fetch_assoc();
$stmt_order->close();

// Si no se encuentra el pedido, es que no pertenece a este usuario o no existe.
if (!$pedido) {
    die("No se encontró el pedido o no tienes permiso para verlo.");
}

//Obtener los productos de ese pedido
$sql_details = "SELECT p.prod_name, od.od_amount, p.prod_price 
                FROM order_details od
                JOIN products p ON od.prod_id = p.prod_id
                WHERE od.ord_id = ?";
$stmt_details = $conexion->prepare($sql_details);
$stmt_details->bind_param("i", $ord_id);
$stmt_details->execute();
$detalles_pedido = $stmt_details->get_result();
$gran_total = 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del Pedido #<?= $pedido['ord_id'] ?> - Bit&Byte</title>
    <link rel="stylesheet" href="../CSS/normalize.css">
    <link rel="stylesheet" href="../CSS/Inicio.css">
    <link rel="stylesheet" href="../CSS/perfil.css"> 
    <link rel="icon" href="../img/favicon.svg" type="image/svg+xml">
</head>
<body>

    <div class="contenedor-principal">
        <main class="perfil-container">
            <h1>Detalle del Pedido #<?= $pedido['ord_id'] ?></h1>
            <a href="perfil.php" class="btn-volver">‹ Volver a mi perfil</a>

            <div class="panel">
                <div class="resumen-pedido-info">
                    <div><strong>Fecha:</strong> <?= date("d/m/Y H:i", strtotime($pedido['ord_date'])) ?></div>
                    <div><strong>Estado:</strong> <span class="estado-pedido"><?= htmlspecialchars($pedido['os_name']) ?></span></div>
                </div>

                <table class="tabla-pedidos tabla-detalle">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($item = $detalles_pedido->fetch_assoc()): 
                            $subtotal = $item['od_amount'] * $item['prod_price'];
                            $gran_total += $subtotal;
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($item['prod_name']) ?></td>
                                <td><?= $item['od_amount'] ?></td>
                                <td>$<?= number_format($item['prod_price'], 2) ?></td>
                                <td>$<?= number_format($subtotal, 2) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="total-label">Total del Pedido:</td>
                            <td class="total-monto">$<?= number_format($gran_total, 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </main>
    </div>

    <footer>
        Derechos Reservados © Bit&Byte
    </footer>
</body>
</html>