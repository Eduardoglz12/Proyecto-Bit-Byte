<?php
    session_start();

    require '../php/db_conexion.php';

    //LÓGICA DE LA PÁGINA 
    $status = $_GET['status'] ?? 'error';
    $mensaje_principal = "";
    $mensaje_detalle = "";
    $titulo = "";
    $icono = "";
    $order_id = null;

    if ($status === 'success') {
        $titulo = "¡Gracias por tu compra!";
        $icono = "✅";
        $mensaje_principal = "Tu pedido ha sido procesado con éxito. Hemos enviado una confirmación a tu correo electrónico.";
        $order_id = $_SESSION['last_order_id'] ?? null;
        unset($_SESSION['last_order_id']);
    } else {
        $titulo = "Error en la Compra";
        $icono = "⚠️";
        $mensaje_principal = "Hubo un problema al procesar tu pago.";
        $mensaje_detalle = $_SESSION['error_compra'] ?? "Por favor, inténtalo de nuevo o contacta a soporte.";
        unset($_SESSION['error_compra']);
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estado de tu Compra - Bit&Byte</title>
    <link rel="stylesheet" href="../CSS/normalize.css">
    <link rel="stylesheet" href="../CSS/Inicio.css">
    <link rel="stylesheet" href="../CSS/gracias.css"> </head>
<body>
    
    <main class="contenedor-principal">
        <div class="gracias-wrapper <?php echo $status; ?>">
            <div class="gracias-icon"><?php echo $icono; ?></div>
            <h1><?php echo $titulo; ?></h1>
            <p class="mensaje">
                <?php echo $mensaje_principal; ?>
                <?php if ($mensaje_detalle): ?>
                    <small><?php echo htmlspecialchars($mensaje_detalle); ?></small>
                <?php endif; ?>
            </p>

            <?php if ($status === 'success' && $order_id): ?>
                <div class="order-id-box">
                    <span>Tu número de pedido es:</span>
                    <strong>#<?php echo $order_id; ?></strong>
                </div>

                <div class="acciones-finales">
                    <a href="../php/generar_recibo.php?id=<?php echo $order_id; ?>" class="btn-volver">Descargar Recibo (PDF)</a>
                </div>

            <?php endif; ?>

            <div class="acciones-finales">
                <a href="../index.php" class="btn-volver">Volver a la tienda</a>
                <?php if (isset($_SESSION['usr_id'])): // Si el usuario está logueado, muestra un enlace a su perfil ?>
                    <a href="perfil.php" class="btn-volver">Ir a mi perfil</a>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer>
        Derechos Reservados © Bit&Byte
    </footer>

</body>
</html>