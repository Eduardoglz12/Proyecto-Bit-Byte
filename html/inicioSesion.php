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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../Css/login.css">
    <link rel="stylesheet" href="../Css/Inicio.css">
    <link rel="stylesheet" href="../Css/normalize.css">
    <link rel="preload" href="../Css/login.css" as="style"> 
    <link rel="preload" href="../Css/Inicio.css" as="style">
    <link rel="preload" href="../Css/normalize.css" as="style">   
    <title>Iniciar sesion</title>

</head>

<body>

  <?php include 'header.php'; ?>

    <div class="cont-principal">
    
    <?php
    if (isset($_SESSION['notificacion'])) {
        $mensaje = $_SESSION['notificacion']['mensaje'];
        $tipo = $_SESSION['notificacion']['tipo'];
        
        echo "<div class='notificacion notificacion-{$tipo}'>" . htmlspecialchars($mensaje) . "</div>";
        
        unset($_SESSION['notificacion']);
    }
    ?>

    <div class="cont-login">
      <form action="../php/manejoInicioS.php" method="post" class="form-login" id="form-login">
        <p>INICIAR SESION</p>
        
        <label for="usr_user">Usuario:</label>
        <input type="text" name="usr_user" id="usr_user">
        <span class="error-texto" id="error-usuario"></span>
        
        <label for="usr_password">Contraseña:</label>
        <input type="password" name="usr_password" id="usr_password">
        <span class="error-texto" id="error-password"></span>
        
        <button type="submit">Iniciar sesion</button>
      </form>
    </div>
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