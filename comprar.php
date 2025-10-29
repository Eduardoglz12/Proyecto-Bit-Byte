<?php
// comprar.php
session_start();
require 'db_conexion.php';

// Si el carrito está vacío, redirigir al inicio
if (empty($_SESSION['carrito'])) {
    header('Location: index.php');
    exit();
}

// Recuperar datos del formulario y errores si existen (para repoblar el formulario)
$error_msg = $_SESSION['error_datos'] ?? null;
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['error_datos'], $_SESSION['form_data']); // Limpiar sesión
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos de Envío - Bit&Byte</title>
    <link rel="stylesheet" href="CSS/normalize.css">
    <link rel="stylesheet" href="CSS/checkout_pasos.css">
 </head>
<body>
    <main class="checkout-container">
        <div class="form-container">
            <h1>Datos de Envío y Contacto</h1>
            <p>Completa la información para continuar con el pago.</p>
            
            <?php if ($error_msg): ?>
                <div class="error-banner"><?= htmlspecialchars($error_msg) ?></div>
            <?php endif; ?>

            <form action="php/validar_datos.php" method="POST" class="customer-form">
                <fieldset>
                    <legend>1. Datos de Contacto</legend>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nombre">Nombre Completo</label>
                            <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($form_data['nombre'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Correo Electrónico</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($form_data['email'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="tel" id="telefono" name="telefono" value="<?= htmlspecialchars($form_data['telefono'] ?? '') ?>" required>
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>2. Dirección de Envío</legend>
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="calle">Calle y Número</label>
                            <input type="text" id="calle" name="calle" value="<?= htmlspecialchars($form_data['calle'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="colonia">Colonia</label>
                            <input type="text" id="colonia" name="colonia" value="<?= htmlspecialchars($form_data['colonia'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="cp">Código Postal</label>
                            <input type="text" id="cp" name="cp" value="<?= htmlspecialchars($form_data['cp'] ?? '') ?>" required>
                        </div>
                    </div>
                     <div class="form-row">
                        <div class="form-group">
                            <label for="ciudad">Ciudad</label>
                            <input type="text" id="ciudad" name="ciudad" value="<?= htmlspecialchars($form_data['ciudad'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="estado">Estado</label>
                            <input type="text" id="estado" name="estado" value="<?= htmlspecialchars($form_data['estado'] ?? '') ?>" required>
                        </div>
                    </div>
                </fieldset>
                
                <button type="submit" class="btn-siguiente">Siguiente</button>
            </form>
        </div>
    </main>
</body>
</html>