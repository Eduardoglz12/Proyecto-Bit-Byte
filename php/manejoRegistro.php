<?php
    session_start();
    //Usamos la conexión centralizada
    require 'db_conexion.php';

    //Variables de formulario
    $usr_user = $_POST['usr_user'];
    $usr_password = $_POST['usr_password'];

    if(!empty($usr_user) && !empty($usr_password)){
        
        //La variable $conexion ya viene de db_conexion.php

        //Hasheamos la contraseña
        $hashed_password = password_hash($usr_password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (usr_user, usr_password) VALUES (?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ss", $usr_user, $hashed_password);

        try{
            if($stmt->execute()){
                $_SESSION['notificacion'] = [
                    'tipo' => 'success',
                    'mensaje' => 'Se realizó el registro con éxito'
                ];
            }
            else{
                $_SESSION['notificacion'] = ['tipo' => 'error', 'mensaje' => 'Error: ' . $stmt->error];
            }
        }
        catch(mysqli_sql_exception $e){
            if($e->getCode() === 1062){
                $_SESSION['notificacion'] = ['tipo' => 'error', 'mensaje' => 'Nombre de usuario no disponible'];
            }
            else{
                $_SESSION['notificacion'] = ['tipo' => 'error', 'mensaje' => 'Error: ' . $e->getMessage()];
            }
        }

        $stmt->close();
        $conexion->close();
    } else {
        $_SESSION['notificacion'] = ['tipo' => 'error', 'mensaje' => 'Usuario y contraseña no pueden estar vacíos.'];
    }

    header('Location: ../html/registro.php');
    exit();
?>

