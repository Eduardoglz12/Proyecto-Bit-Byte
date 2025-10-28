<?php
  session_start();
  $resultado = "";

  if(isset($_SESSION['resultado'])){
    $resultado = $_SESSION['resultado'];
    unset($_SESSION['resultado']);
  }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/login.css">
    <title>Resgistro</title>
</head>

<body>

  <div class="cont-principal">
    <?php if(!empty($resultado)): ?>
      <p class="resultado"><?php echo"$resultado"; ?></p>
    <?php endif;?>
    <div class="cont-login">
      <form action="manejoRegistro.php" method="post">
        <p>CREAR USUARIO</p>
        <label for="usr_user">Usuario:</label>
        <input type="text" name="usr_user" id="usr_user">
        <label for="usr_password">Contraseña:</label>
        <input type="text" name="usr_password" id="usr_password">
        <button type="submit">Registrarme</button>
      </form>
    </div>
  </div>
    
</body>
</html>