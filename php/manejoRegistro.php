<?php
    session_start();
    require 'db_conexion.php';

    //Variables de formulario
    $usr_user = $_POST['usr_user'];
    $usr_password = $_POST['usr_password'];
    $usr_password_confirm = $_POST['usr_password_confirm'];

    //Validar que no estén vacíos (igual que en JS)
    if(empty($usr_user) || empty($usr_password)) {
        $_SESSION['notificacion'] = ['tipo' => 'error', 'mensaje' => 'Usuario y contraseña no pueden estar vacíos.'];
        header('Location: ../html/registro.php');
        exit();
    }

    //Verificar que las contraseñas coincidan en el servidor
    if ($usr_password !== $usr_password_confirm) {
        $_SESSION['notificacion'] = ['tipo' => 'error', 'mensaje' => 'Las contraseñas no coinciden.'];
        header('Location: ../html/registro.php');
        exit();
    }

    //Longitud de contraseña
    if (strlen($usr_password) < 6) {
        $_SESSION['notificacion'] = ['tipo' => 'error', 'mensaje' => 'La contraseña debe tener al menos 6 caracteres.'];
        header('Location: ../html/registro.php');
        exit();
    }
    
    // Si todo está bien, procedemos a insertar
    $hashed_password = password_hash($usr_password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (usr_user, usr_password) VALUES (?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ss", $usr_user, $hashed_password);

    try{
        if($stmt->execute()){
            $_SESSION['notificacion'] = [
                'tipo' => 'success',
                'mensaje' => 'Se realizó el registro con éxito. Ya puedes iniciar sesión.'
            ];
            // Lo redirigimos al login para que inicie sesión
            header('Location: ../html/inicioSesion.php'); 
            exit();
        }
    }
    catch(mysqli_sql_exception $e){
        if($e->getCode() === 1062){ // Error de entrada duplicada
            $_SESSION['notificacion'] = ['tipo' => 'error', 'mensaje' => 'Ese nombre de usuario ya existe. Elige otro.'];
        }
        else{
            $_SESSION['notificacion'] = ['tipo' => 'error', 'mensaje' => 'Error: ' . $e->getMessage()];
        }
        header('Location: ../html/registro.php');
        exit();
    }

    $stmt->close();
    $conexion->close();
?>

