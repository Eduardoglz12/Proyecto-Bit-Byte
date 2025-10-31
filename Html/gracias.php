<?php
    session_start();

    // Esta página ya no procesa, solo muestra el resultado.
    // La lógica real está en procesar_pedido.php

    $status = isset($_GET['status']) ? $_GET['status'] : 'error';
    $mensaje = "";
    $order_id = null;

    if ($status === 'success') {
        $mensaje = "¡Tu pedido ha sido procesado con éxito!";
        $order_id = isset($_SESSION['last_order_id']) ? $_SESSION['last_order_id'] : null;
        unset($_SESSION['last_order_id']); // Limpiar para futuras compras
    } else {
        $mensaje = "Hubo un problema con tu compra.";
        if (isset($_SESSION['error_compra'])) {
            $mensaje .= "<br><small>" . htmlspecialchars($_SESSION['error_compra']) . "</small>";
            unset($_SESSION['error_compra']); // Limpiar
        }
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estado de tu Compra</title>
    <link rel="stylesheet" href="../CSS/Inicio.css">
    <style>
        .gracias-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 60vh;
            text-align: center;
            background-color: var(--gris-oscuro);
            padding: 4rem;
            border-radius: 0.8rem;
        }
        .gracias-container.success h1 {
            color: var(--azul-claro);
        }
        .gracias-container.error h1 {
            color: var(--rojo-error);
        }
        .gracias-container p {
            font-size: 1.8rem;
            max-width: 600px;
        }
        .gracias-container a.btn-checkout {
            margin-top: 2rem;
            text-decoration: none;
            display: inline-block;
        }
    </style>
</head>
<body>
    <header>
        </header>
    <main class="contenedor-principal">
        <div class="gracias-container <?php echo $status; ?>">
            <h1><?php echo ($status === 'success' ? '✅ ¡Gracias por tu compra!' : '❌ Error en la Compra'); ?></h1>
            <p><?php echo $mensaje; ?></p>
            <?php if ($status === 'success' && $order_id): ?>
                <p>Tu número de pedido es: <strong>#<?php echo $order_id; ?></strong></p>
            <?php endif; ?>
            <a href="../index.php" class="btn-checkout">Volver a la tienda</a>
        </div>
    </main>
    <footer>
        Derechos Reservados © Bit&Byte
    </footer>
</body>
</html>