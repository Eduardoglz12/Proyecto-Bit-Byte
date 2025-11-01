<?php
    session_start();
    //Usar la conexión centralizada
    require 'db_conexion.php';

    //Recibir todas las variables del formulario
    $prod_name = $_POST['prod_name'];
    $prod_imagen_url = $_POST['prod_imagen_url'];
    $prod_spec_url = $_POST['prod_spec_url'];
    $prod_stock = $_POST['prod_stock'];
    $prod_price = $_POST['prod_price'];

    //Validar que todos los campos, incluido el nuevo, no estén vacíos
    if(!empty($prod_name) && !empty($prod_imagen_url) && !empty($prod_spec_url) && !empty($prod_stock) && !empty($prod_price)){
        
        // La variable $conexion ya viene de db_conexion.php

        //Actualizar la consulta SQL para incluir la nueva columna
        $sql = "INSERT INTO products(prod_name, prod_imagen_url, prod_spec_url, prod_stock, prod_price)" .
               "VALUES (?, ?, ?, ?, ?)";

        $stmt = $conexion->prepare($sql);
        
        //Actualizar los tipos de datos en bind_param para incluir el nuevo string ("sssdi")
        $stmt->bind_param("sssid", $prod_name, $prod_imagen_url, $prod_spec_url, $prod_stock, $prod_price);

        try{
            if($stmt->execute()){
                $_SESSION['resultado'] = "Se ingresó el producto con éxito.";
            }
            else{
                $_SESSION['resultado'] = "Error al ejecutar la consulta: " . $stmt->error;
            }
        }
        catch(mysqli_sql_exception $e){
                $_SESSION['resultado'] = "Error de base de datos: " . $e->getMessage();
        }

        $stmt->close();
        $conexion->close();
    } else {
        $_SESSION['resultado'] = "Error: Todos los campos son obligatorios.";
    }

    // Redirigir de vuelta al panel de inventario
    header('Location: ../html/inventario.php');
    exit();
?>