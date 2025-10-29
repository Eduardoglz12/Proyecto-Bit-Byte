<?php
session_start();
require 'db_conexion.php';

// Si el carrito está vacío, redirigir al inicio
if (empty($_SESSION['carrito'])) {
    header('Location: index.php');
    exit();
}

// --- Sesión de Usuario ---
$textoSesion1 = "Iniciar sesión";
$linkSesion1 = "php/inicioSesion.php";
$textoSesion2 = "Registrarme";
$linkSesion2 = "php/registro.php";

if (isset($_SESSION['usr_user'])) {
    $textoSesion1 = $_SESSION['usr_user'];
    $linkSesion1 = "#";
    $textoSesion2 = "Cerrar sesión";
    $linkSesion2 = "php/cerrarSesion.php";
}

// --- Carrito ---
$totalItemsCarrito = array_sum($_SESSION['carrito']);
$productos_en_carrito = [];
$gran_total = 0.0;

if (!empty($_SESSION['carrito'])) {
    $product_ids = array_keys($_SESSION['carrito']);
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    
    $sql = "SELECT prod_id, prod_name, prod_imagen_url, prod_price FROM products WHERE prod_id IN ($placeholders)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param(str_repeat('i', count($product_ids)), ...$product_ids);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    while ($producto = $resultado->fetch_assoc()) {
        $productos_en_carrito[$producto['prod_id']] = $producto;
    }
    $stmt->close();
}
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Finalizar Compra - Bit&Byte</title>
<link rel="icon" href="img/favicon.svg" type="image/svg+xml">
<link rel="stylesheet" href="CSS/Inicio.css">
<link rel="stylesheet" href="CSS/normalize.css">
</head>
<body>
<header></header>
<main>
<div class="contenedor-principal">
    <div class="checkout-container">
        <!-- Resumen del Pedido -->
        <div class="resumen-pedido">
            <h2>Resumen de tu Pedido</h2>
            <?php foreach ($_SESSION['carrito'] as $prod_id => $cantidad): ?>
                <?php if (isset($productos_en_carrito[$prod_id])):
                    $producto = $productos_en_carrito[$prod_id];
                    $subtotal = $producto['prod_price'] * $cantidad;
                    $gran_total += $subtotal;
                ?>
                    <div class="resumen-producto">
                        <img src="<?= htmlspecialchars($producto['prod_imagen_url']); ?>" alt="<?= htmlspecialchars($producto['prod_name']); ?>" class="resumen-img">
                        <div class="resumen-info">
                            <p class="resumen-nombre"><?= htmlspecialchars($producto['prod_name']); ?></p>
                            <p class="resumen-cantidad">Cantidad: <?= $cantidad; ?></p>
                        </div>
                        <p class="resumen-subtotal">$<?= number_format($subtotal, 2); ?></p>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
            <div class="resumen-total">
                <p>Total a Pagar:</p>
                <p class="total-monto">$<?= number_format($gran_total, 2); ?></p>
            </div>
        </div>

        <!-- Formulario Cliente -->
        <div class="form-datos-cliente">
            <h2>Información de Contacto y Envío</h2>
            <form id="checkout-form">
                <div class="campo">
                    <label for="nombre">Nombre Completo</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="campo">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="campo">
                    <label for="telefono">Teléfono de Contacto</label>
                    <input type="tel" id="telefono" name="telefono" required>
                </div>
                <div class="campo">
                    <label for="direccion">Dirección de Envío</label>
                    <textarea id="direccion" name="direccion" rows="3" required></textarea>
                </div>

                <h2>Método de Pago</h2>
                <div class="opciones-pago">
                    <label>
                        <input type="radio" name="metodo_pago" value="tarjeta" checked>
                        💳 Tarjeta de Crédito/Débito
                    </label>
                    <label>
                        <input type="radio" name="metodo_pago" value="paypal">
                        <img src="https://www.paypalobjects.com/images/shared/paypal-logo-129x32.svg" alt="PayPal" style="height:24px;">
                    </label>
                </div>

                <div id="pago-tarjeta" class="metodo-pago-form">
                    <p style="text-align:center;color:var(--azul-claro);">Formulario de tarjeta deshabilitado para demostración.</p>
                    <button type="submit" class="btn-checkout disabled" disabled>Finalizar Compra</button>
                </div>
                <div id="pago-paypal" class="metodo-pago-form" style="display:none;">
                    <div id="paypal-button-container"></div>
                </div>
            </form>
        </div>
    </div>
</div>
</main>
<footer>
Derechos Reservados © Bit&Byte
</footer>

<script src="https://www.paypal.com/sdk/js?client-id=ASUajecFhJzfxHxdX4POf20OweQ_rqAY2zMB02SPs1Sq6EJ9loM2upMo5YcQW8GEw3_UMfWes7_I7yao&currency=MXN"></script>

<script>
// Mostrar/Ocultar métodos de pago
document.querySelectorAll('input[name="metodo_pago"]').forEach(radio => {
    radio.addEventListener('change', e => {
        document.getElementById('pago-tarjeta').style.display = 'none';
        document.getElementById('pago-paypal').style.display = 'none';
        if (e.target.value === 'tarjeta') {
            document.getElementById('pago-tarjeta').style.display = 'block';
        } else if (e.target.value === 'paypal') {
            document.getElementById('pago-paypal').style.display = 'block';
        }
    });
});

// Botón PayPal Sandbox
paypal.Buttons({
    style: {
        layout: 'vertical',
        color: 'blue',
        shape: 'rect',
        label: 'pay'
    },
    createOrder: function(data, actions) {
        return actions.order.create({
            purchase_units: [{
                amount: { value: <?= json_encode($gran_total); ?> }
            }]
        });
    },
    onApprove: function(data, actions) {
        return actions.order.capture().then(function(details) {
            // Redirigir a procesar pedido con orderID
            window.location.href = "php/procesar_pedido.php?orderID=" + data.orderID;
        });
    }
}).render('#paypal-button-container');
</script>
</body>
</html>
