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
        // Creamos la conexion
        $conexion = new mysqli($dbhost, $dbuser, $dbpass, $dbname, $dbport);

        // Hasheamos la contraseña
        $hashed_password = password_hash($usr_password, PASSWORD_DEFAULT);

        // Usamos sentencias preparadas para evitar inyeccion sql
        $sql = "INSERT INTO users (usr_user, usr_password) VALUES (?, ?)";
        $stmt = $conexion->prepare($sql);
        // Vincular parametros. 's' - string
        $stmt->bind_param("ss", $usr_user, $hashed_password);

        try{
            if($stmt->execute()){
                $_SESSION['resultado'] = "Se realizo el registro con exito";
            }
            else{
                $_SESSION['resultado'] = "Error: " . $stmt->error;
            }
        }
        catch(mysqli_sql_exception $e){
            if($e->getCode() === 1062){
                $_SESSION['resultado'] = "Nombre de usuario no disponible";
            }
            else{
                $_SESSION['resultado'] = "Error: " . $e->getMessage();
            }
        }

        $stmt->close();
        $conexion->close();
    }

    header('Location: registro.php');
?>