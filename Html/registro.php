<?php
  session_start();
  $resultado = "";

  if(isset($_SESSION['resultado'])){
    $resultado = $_SESSION['resultado'];
    unset($_SESSION['resultado']);
  }

  //Lógica de Sesión
  $textoSesion1 = "Iniciar sesion";
  $linkSesion1 = "inicioSesion.php";
  $textoSesion2 = "Registrarme";
  $linkSesion2 = "registro.php";

    //LÓGICA PARA EL HEADER
if (isset($_SESSION['usr_user'])) {
    $textoSesion1 = $_SESSION['usr_user'];
    $linkSesion1 = "perfil.php";
    $textoSesion2 = "Cerrar sesión";
    $linkSesion2 = "../Php/cerrarSesion.php";
    }
    
    
  //Lógica del Carrito (para el contador)
  $totalItemsCarrito = 0;
  if (isset($_SESSION['carrito']) && is_array($_SESSION['carrito'])) {
      $totalItemsCarrito = array_sum($_SESSION['carrito']);
  }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/login.css">
    <link rel="stylesheet" href="../CSS/Inicio.css" as="style">
    <link rel="icon" href="../img/favicon.svg" type="image/svg+xml">
    <title>Registro</title>
</head>

<body>

  <?php include 'header.php'; ?>

  <div class="cont-principal">

    <?php
    // Lee la notificación de la sesión
    if (isset($_SESSION['notificacion'])) {
        $mensaje = $_SESSION['notificacion']['mensaje'];
        $tipo = $_SESSION['notificacion']['tipo'];
        
        // Muestra el div con la clase de tipo correcta
        echo "<div class='notificacion notificacion-{$tipo}'>" . htmlspecialchars($mensaje) . "</div>";
        
        // Limpia la sesión
        unset($_SESSION['notificacion']);
    }
    ?>

    <div class="cont-login">
      <form action="../Php/manejoRegistro.php" method="post" class="form-login">
        <p>CREAR USUARIO</p>
        <label for="usr_user">Usuario:</label>
        <input type="text" name="usr_user" id="usr_user">
        <label for="usr_password">Contraseña:</label>
        <input type="password" name="usr_password" id="usr_password">
        <button type="submit">Registrarme</button>
      </form>
    </div>
  </div>

  <footer>
        Derechos Reservados © Bit&Byte
    </footer>
    
</body>
</html>