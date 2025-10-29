<?php
// seleccionar_pago.php
session_start();

// Si el carrito está vacío o no hay datos de cliente, no se puede estar aquí
if (empty($_SESSION['carrito']) || empty($_SESSION['datos_cliente'])) {
    header('Location: index.php');
    exit();
}

$datos_cliente = $_SESSION['datos_cliente'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Pago - Bit&Byte</title>
    <link rel="stylesheet" href="CSS/normalize.css">
    <link rel="stylesheet" href="CSS/checkout_pasos.css">
</head>
<body>
    <main class="checkout-container">
        <div class="form-container">
            <h1>Método de Pago</h1>
            <p>Tus datos han sido guardados. Por favor, elige cómo quieres pagar.</p>

            <div class="datos-resumen">
                <strong>Enviar a:</strong> <?= htmlspecialchars($datos_cliente['nombre']) ?><br>
                <?= htmlspecialchars($datos_cliente['calle']) ?>, <?= htmlspecialchars($datos_cliente['colonia']) ?><br>
                <?= htmlspecialchars($datos_cliente['ciudad']) ?>, <?= htmlspecialchars($datos_cliente['estado']) ?>, C.P. <?= htmlspecialchars($datos_cliente['cp']) ?>
                <br><a href="comprar.php">Cambiar datos</a>
            </div>

            <div class="payment-methods">
                <div class="payment-box">
                    <h3>💳 Tarjeta de Crédito/Débito</h3>
                    <p class="aviso">Esta opción estará disponible próximamente.</p>
                    <button class="btn-pago-disabled" disabled>Pagar con Tarjeta</button>
                </div>

                <div class="payment-box">
                    <h3>
                        <img src="https://www.paypalobjects.com/images/shared/paypal-logo-129x32.svg" alt="PayPal">
                    </h3>
                    <p class="aviso">Serás redirigido a PayPal para completar tu pago de forma segura.</p>
                    <form action="php/iniciar_pago.php" method="POST">
                        <button type="submit" class="btn-pago-paypal">Pagar con PayPal</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>
</html>