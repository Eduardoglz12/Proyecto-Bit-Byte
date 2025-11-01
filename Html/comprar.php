<?php
// comprar.php
session_start();
require '../php/db_conexion.php';

if (empty($_SESSION['carrito'])) {
    header('Location: index.php');
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

//BLOQUE PARA CARGAR DATOS DEL USUARIO
$datos_usuario = [];
if (isset($_SESSION['usr_id'])) {
    $usr_id = $_SESSION['usr_id'];
    $sql = "SELECT usr_nombre_completo, usr_email, usr_telefono, usr_calle, usr_colonia, usr_ciudad, usr_estado, usr_cp FROM users WHERE usr_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $usr_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    if ($resultado->num_rows > 0) {
        $datos_usuario = $resultado->fetch_assoc();
    }
    $stmt->close();
}

// Recuperar datos del formulario si hubo un error de validación
$error_msg = $_SESSION['error_datos'] ?? null;
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['error_datos'], $_SESSION['form_data']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bit&Byte - Comprar</title>
  <link rel="icon" href="../img/favicon.svg" type="image/svg+xml">
  <link rel="stylesheet" href="../CSS/Inicio.css">
  <link rel="stylesheet" href="../CSS/normalize.css">
  <link rel="stylesheet" href="../CSS/checkout_pasos.css">
  <link rel="preload" href="../CSS/Inicio.css" as="style">
  <link rel="preload" href="../CSS/normalize.css" as="style">
</head>
<body>

  <?php include 'header.php'; ?>

    <div class="contenedor-principal">
        <main class="checkout-container">
            <div class="form-container">
                <h1>Datos de Envío y Contacto</h1>
                <p>Completa tu información para poder continuar con el pago.</p>
                
                <?php if ($error_msg): ?>
                    <div class="error-banner"><?= htmlspecialchars($error_msg) ?></div>
                <?php endif; ?>

                <form action="../php/validar_datos.php" method="POST" class="customer-form">
                    <fieldset>
                        <legend>1. Información de Contacto</legend>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nombre">Nombre Completo</label>
                                <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($datos_usuario['usr_nombre_completo'] ?? $form_data['nombre'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Correo Electrónico</label>
                                <input type="email" id="email" name="email" value="<?= htmlspecialchars($datos_usuario['usr_email'] ?? $form_data['email'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="telefono">Teléfono de Contacto</label>
                                <input type="tel" id="telefono" name="telefono" value="<?= htmlspecialchars($datos_usuario['usr_telefono'] ?? $form_data['telefono'] ?? '') ?>" required>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>2. Dirección de Envío</legend>
                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="calle">Calle y Número</label>
                                <input type="text" id="calle" name="calle" value="<?= htmlspecialchars($datos_usuario['usr_calle'] ?? $form_data['calle'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="colonia">Colonia</label>
                                <input type="text" id="colonia" name="colonia" value="<?= htmlspecialchars($datos_usuario['usr_colonia'] ?? $form_data['colonia'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="cp">Código Postal</label>
                                <input type="text" id="cp" name="cp" value="<?= htmlspecialchars($datos_usuario['usr_cp'] ?? $form_data['cp'] ?? '') ?>" required>
                            </div>
                        </div>
                         <div class="form-row">
                            <div class="form-group">
                                <label for="ciudad">Ciudad</label>
                                <input type="text" id="ciudad" name="ciudad" value="<?= htmlspecialchars($datos_usuario['usr_ciudad'] ?? $form_data['ciudad'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="estado">Estado</label>
                                <input type="text" id="estado" name="estado" value="<?= htmlspecialchars($datos_usuario['usr_estado'] ?? $form_data['estado'] ?? '') ?>" required>
                            </div>
                        </div>
                    </fieldset>
                    
                    <button type="submit" class="btn-siguiente">Siguiente: Elegir Pago</button>
                </form>
            </div>
        </main>
    </div>

    <footer>
        Derechos Reservados © Bit&Byte
    </footer>

    </body>
</html>