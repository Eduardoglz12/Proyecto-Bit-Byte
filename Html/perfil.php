<?php
session_start();
require '../php/db_conexion.php';

    // --- LÓGICA PARA EL HEADER ---
if (isset($_SESSION['usr_user'])) {
    $textoSesion1 = $_SESSION['usr_user'];
    $linkSesion1 = "perfil.php"; 
    $textoSesion2 = "Cerrar sesión";
    $linkSesion2 = "../php/cerrarSesion.php";
    }

      // --- Lógica del Carrito (para el contador) ---
  $totalItemsCarrito = 0;
  if (isset($_SESSION['carrito']) && is_array($_SESSION['carrito'])) {
      $totalItemsCarrito = array_sum($_SESSION['carrito']);
  }
    

// 1. Seguridad: Si el usuario no está logueado, redirigir al inicio de sesión.
if (!isset($_SESSION['usr_id'])) {
    header('Location: inicioSesion.php');
    exit();
}

$usr_id = $_SESSION['usr_id'];

// 2. Mensaje de resultado (si se actualizaron los datos)
$resultado_msg = $_SESSION['resultado_perfil'] ?? null;
unset($_SESSION['resultado_perfil']);

// 3. Obtener los datos actuales del usuario para mostrarlos en el formulario.
$sql_user = "SELECT usr_nombre_completo, usr_email, usr_telefono, usr_calle, usr_colonia, usr_ciudad, usr_estado, usr_cp FROM users WHERE usr_id = ?";
$stmt_user = $conexion->prepare($sql_user);
$stmt_user->bind_param("i", $usr_id);
$stmt_user->execute();
$datos_usuario = $stmt_user->get_result()->fetch_assoc();
$stmt_user->close();

// 4. Obtener el historial de pedidos del usuario.
$sql_orders = "SELECT ord_id, ord_date, os_name FROM orders 
               JOIN order_status ON orders.os_id = order_status.os_id
               WHERE usr_id = ? ORDER BY ord_date DESC";
$stmt_orders = $conexion->prepare($sql_orders);
$stmt_orders->bind_param("i", $usr_id);
$stmt_orders->execute();
$historial_pedidos = $stmt_orders->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil - Bit&Byte</title>
    <link rel="icon" href="../img/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="../CSS/normalize.css">
    <link rel="stylesheet" href="../CSS/Inicio.css">
    <link rel="stylesheet" href="../CSS/perfil.css"> 
</head>
<body>
    
    <?php include 'header.php'; ?>

    <div class="contenedor-principal">
        <main class="perfil-container">
            <h1>Mi Perfil</h1>

            <?php if ($resultado_msg): ?>
                <div class="resultado-banner"><?= htmlspecialchars($resultado_msg) ?></div>
            <?php endif; ?>

            <div class="panel datos-panel">
                <h2>Mis Datos</h2>
                <form action="../php/actualizar_perfil.php" method="POST" class="form-perfil">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nombre">Nombre Completo</label>
                            <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($datos_usuario['usr_nombre_completo'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Correo Electrónico</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($datos_usuario['usr_email'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="tel" id="telefono" name="telefono" value="<?= htmlspecialchars($datos_usuario['usr_telefono'] ?? '') ?>" required>
                        </div>
                    </div>
                    <hr>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="calle">Calle y Número</label>
                            <input type="text" id="calle" name="calle" value="<?= htmlspecialchars($datos_usuario['usr_calle'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="colonia">Colonia</label>
                            <input type="text" id="colonia" name="colonia" value="<?= htmlspecialchars($datos_usuario['usr_colonia'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="ciudad">Ciudad</label>
                            <input type="text" id="ciudad" name="ciudad" value="<?= htmlspecialchars($datos_usuario['usr_ciudad'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="estado">Estado</label>
                            <input type="text" id="estado" name="estado" value="<?= htmlspecialchars($datos_usuario['usr_estado'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="cp">Código Postal</label>
                            <input type="text" id="cp" name="cp" value="<?= htmlspecialchars($datos_usuario['usr_cp'] ?? '') ?>" required>
                        </div>
                    </div>
                    <button type="submit" class="btn-guardar">Guardar Cambios</button>
                </form>
            </div>

            <div class="panel pedidos-panel">
                <h2>Historial de Pedidos</h2>
                <?php if ($historial_pedidos->num_rows > 0): ?>
                    <table class="tabla-pedidos">
                        <thead>
                            <tr>
                                <th>ID Pedido</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($pedido = $historial_pedidos->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?= $pedido['ord_id'] ?></td>
                                    <td><?= date("d/m/Y", strtotime($pedido['ord_date'])) ?></td>
                                    <td><span class="estado-pedido"><?= htmlspecialchars($pedido['os_name']) ?></span></td>
                                    <td>
                                        <a href="pedido_detalle.php?id=<?= $pedido['ord_id'] ?>" class="btn-ver-detalle">Ver Detalle</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Aún no has realizado ningún pedido.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <footer>
        Derechos Reservados © Bit&Byte
    </footer>
</body>
</html>