<?php
  session_start();
  $mensajeError = "";

  if(isset($_SESSION['mensajeError'])){
    $mensajeError = $_SESSION['mensajeError'];
    unset($_SESSION['mensajeError']);
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="../CSS/login.css">
    <link rel="preload" href="../CSS/login.css" as="style">    
    <title>Iniciar sesion</title>
</head>

<body>
  <div class="cont-principal">
    <?php if(!empty($mensajeError)): ?>
      <p class="mensajeError"><?php echo"$mensajeError"; ?></p>
    <?php endif; ?>
    <div class="cont-login">
      <form action="manejoInicioS.php" method="post">
        <p>INICIAR SESION</p>
        <label for="usr_user">Usuario:</label>
        <input type="text" name="usr_user" id="usr_user">
        <label for="usr_password">Contraseña:</label>
        <input type="password" name="usr_password" id="usr_password">
        <button type="submit">Iniciar sesion</button>
      </form>
    </div>
  </div>
    
</body>
</html>