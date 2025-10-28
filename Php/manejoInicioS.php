<?php
    session_start();

    // Variables base de datos 
    $dbhost = "localhost";
    $dbuser = "root";
    $dbpass = "";
    $dbname = "bitandbyte";
    $dbport = "3306";

        // Variables de formulario
    $usr_user = $_POST['usr_user'];
    $usr_password = $_POST['usr_password'];

    if(!empty($usr_user) && !empty($usr_password)){
        $conexion = new mysqli($dbhost, $dbuser, $dbpass, $dbname, $dbport);

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

                if($usr_user == "admin"){
                    header('Location: inventario.php');
                    exit();
                }

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
    }

    header('Location: inicioSesion.php');
?>