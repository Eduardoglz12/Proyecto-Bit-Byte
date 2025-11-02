<?php
  session_start();
  require '../php/db_conexion.php'; // Incluimos la conexión
  $mensajeError = "";

  if(isset($_SESSION['mensajeError'])){
    $mensajeError = $_SESSION['mensajeError'];
    unset($_SESSION['mensajeError']);
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
    $linkSesion2 = "../php/cerrarSesion.php";
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
    <link rel="icon" href="../img/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="../CSS/login.css">
    <link rel="stylesheet" href="../CSS/Inicio.css">
    <link rel="stylesheet" href="../CSS/normalize.css">
    <link rel="preload" href="../CSS/login.css" as="style"> 
    <link rel="preload" href="../CSS/Inicio.css" as="style">
    <link rel="preload" href="../CSS/normalize.css" as="style">   
    <title>Iniciar sesion</title>

</head>

<body>

  <?php include 'header.php'; ?>

  <div class="cont-principal">
    <?php if(!empty($mensajeError)): ?>
      <p class="mensajeError"><?php echo"$mensajeError"; ?></p>
    <?php endif; ?>
    <div class="cont-login">
      <form action="../php/manejoInicioS.php" method="post" class="form-login">
        <p>INICIAR SESION</p>
        <label for="usr_user">Usuario:</label>
        <input type="text" name="usr_user" id="usr_user">
        <label for="usr_password">Contraseña:</label>
        <input type="password" name="usr_password" id="usr_password">
        <button type="submit">Iniciar sesion</button>
      </form>
    </div>
  </div>

  <footer>
        Derechos Reservados © Bit&Byte
  </footer>
    
</body>
</html>