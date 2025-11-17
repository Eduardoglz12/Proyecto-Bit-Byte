<?php
// seleccionar_pago.php
session_start();
  require '../php/db_conexion.php';

// Si el carrito está vacío o no se han ingresado los datos del cliente, no se puede estar aquí.
if (empty($_SESSION['carrito']) || empty($_SESSION['datos_cliente'])) {
    header('Location: ../index.php');
    exit();
}

//LÓGICA PARA EL HEADER
//Sesión de Usuario
$textoSesion1 = "Iniciar sesión";
$linkSesion1 = "inicioSesion.php";
$textoSesion2 = "Registrarme";
$linkSesion2 = "registro.php";

if (isset($_SESSION['usr_user'])) {
    $textoSesion1 = $_SESSION['usr_user'];
    $linkSesion1 = "perfil.php";
    $textoSesion2 = "Cerrar sesión";
    $linkSesion2 = "../php/cerrarSesion.php";
}

//Total de artículos en el carrito
$totalItemsCarrito = 0;
if (!empty($_SESSION['carrito'])) {
    $totalItemsCarrito = array_sum($_SESSION['carrito']);
}

// --- LÓGICA PARA LA PÁGINA ---
$datos_cliente = $_SESSION['datos_cliente'];

//LEER Y LIMPIAR EL MENSAJE DE ERROR
$error_tarjeta = $_SESSION['error_tarjeta'] ?? null;
unset($_SESSION['error_tarjeta']); // Limpiamos la sesión para no mostrar el error de nuevo
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bit&Byte - Comprar</title>
  <link rel="icon" href="../img/favicon.svg" type="image/svg+xml">
  <link rel="stylesheet" href="../Css/Inicio.css">
  <link rel="stylesheet" href="../Css/normalize.css">
  <link rel="stylesheet" href="../Css/checkout_pasos.css">
  <link rel="preload" href="../Css/Inicio.css" as="style">
  <link rel="preload" href="../Css/normalize.css" as="style">
</head>
<body>

  <?php include 'header.php'; ?>

    <div class="contenedor-principal">
        <main class="checkout-container">
            <div class="form-container">
                <h1>Método de Pago</h1>
                <p>Tus datos han sido guardados. Por favor, elige cómo quieres pagar.</p>

                <?php if ($error_tarjeta): ?>
                    <div class="error-banner"><?= htmlspecialchars($error_tarjeta) ?></div>
                <?php endif; ?>

                <div class="datos-resumen">
                    <strong>Enviar a:</strong> <?= htmlspecialchars($datos_cliente['nombre']) ?><br>
                    <?= htmlspecialchars($datos_cliente['calle']) ?>, <?= htmlspecialchars($datos_cliente['colonia']) ?><br>
                    <?= htmlspecialchars($datos_cliente['ciudad']) ?>, <?= htmlspecialchars($datos_cliente['estado']) ?>, C.P. <?= htmlspecialchars($datos_cliente['cp']) ?>
                    <br><a href="comprar.php">Cambiar datos</a>
                </div>

                <div class="payment-methods">
                    <div class="payment-box">
                        <h3>💳 Tarjeta de Crédito/Débito</h3>
                        
                        <form action="../php/procesar_pago_tarjeta.php" method="POST" class="tarjeta-form" id="form-tarjeta">
                            <div class="form-group">
                                <label for="nombre_tarjeta">Nombre del Titular</label>
                                <input type="text" id="nombre_tarjeta" name="nombre_tarjeta" required>
                                <span class="error-texto" id="error-nombre-tarjeta"></span>
                            </div>
                            <div class="form-group">
                                <label for="numero_tarjeta">Número de Tarjeta</label>
                                <input type="text" id="numero_tarjeta" name="numero_tarjeta" placeholder="0000 0000 0000 0000" maxlength="19" required>
                                <span class="error-texto" id="error-numero-tarjeta"></span>
                            </div>
                            <div class="tarjeta-form-row">
                                <div class="form-group">
                                    <label>Fecha de Vencimiento</label>
                                    <div class="fecha-grupo">
                                        <select name="mes_vencimiento" id="mes_vencimiento" required>
                                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                                <option value="<?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>"><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?></option>
                                            <?php endfor; ?>
                                        </select>
                                        <span>/</span>
                                        <select name="ano_vencimiento" id="ano_vencimiento" required>
                                            <?php $ano_actual = date('Y'); ?>
                                            <?php for ($i = 0; $i < 10; $i++): ?>
                                                <option value="<?= $ano_actual + $i ?>"><?= $ano_actual + $i ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <span class_name="error-texto" id="error-fecha"></span>
                                </div>
                                <div class="form-group">
                                    <label for="cvv">CVV</label>
                                    <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="4" required>
                                    <span class_name="error-texto" id="error-cvv"></span>
                                </div>
                            </div>
                            <button type="submit" class="btn-pago-tarjeta">Pagar con Tarjeta</button>
                        </form>
                    </div>

                    <div class="payment-box">
                        <h3>
                            <img src="https://www.paypalobjects.com/images/shared/paypal-logo-129x32.svg" alt="PayPal">
                        </h3>
                        <p class="aviso">Serás redirigido a PayPal para completar tu pago de forma segura.</p>
                        <form action="../php/iniciar_pago.php" method="POST">
                            <button type="submit" class="btn-pago-paypal">Pagar con PayPal</button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <footer>
        <a href="#" id="btn-volver-arriba">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 15.41L12 10.83l4.59 4.58L18 14l-6-6-6 6z"></path></svg>
        </a> 
        Derechos Reservados © Bit&Byte
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../js/main.js"></script>
    
</body>
</html>