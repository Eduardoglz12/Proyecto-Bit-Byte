<?php
    session_start();
    // 1. Usamos la conexión centralizada
    require 'db_conexion.php';

    // Variables de formulario
    $usr_user = $_POST['usr_user'];
    $usr_password = $_POST['usr_password'];

    if(!empty($usr_user) && !empty($usr_password)){
        
        // 2. La variable $conexion ya viene de db_conexion.php

        $sql = "SELECT usr_id, usr_password FROM users " .
               "WHERE usr_user = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $usr_user);
        $stmt->execute();

        $resultado = $stmt->get_result();

        if($resultado->num_rows === 1){
            $fila = $resultado->fetch_assoc();

            $usr_id = $fila['usr_id'];
            $hashed_password = $fila['usr_password'];

            if(password_verify($usr_password, $hashed_password)){
                $_SESSION['usr_id'] = $usr_id;
                $_SESSION['usr_user'] = $usr_user;

                // Redirigir al admin al inventario
                if($usr_user == "admin"){
                    header('Location: ../html/inventario.php');
                    exit();
                }

                // Redirigir a otros usuarios al index
                header('Location: ../index.php');
                exit();
            }
            else{
                $_SESSION['mensajeError'] = "Contraseña incorrecta";
            }
        }
        else{
            $_SESSION['mensajeError'] = "Usuario no encontrado";
        }
        $stmt->close();
        $conexion->close();
    } else {
        $_SESSION['mensajeError'] = "Usuario y contraseña requeridos.";
    }

    // Si algo falla, regresa a la página de login
    header('Location: ../html/inicioSesion.php');
    exit();
?>

