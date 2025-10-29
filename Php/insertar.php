<?php
    session_start();
    // 1. Usamos la conexión centralizada
    require_once __DIR__ . '/../db_conexion.php';

    $prod_name = $_POST['prod_name'];
    $prod_imagen_url = $_POST['prod_imagen_url']; // Campo de imagen añadido
    $prod_stock = $_POST['prod_stock'];
    $prod_price = $_POST['prod_price'];

    if(!empty($prod_name) && !empty($prod_imagen_url) && !empty($prod_stock) && !empty($prod_price)){
        
        // 2. La variable $conexion ya viene de db_conexion.php

        $sql = "INSERT INTO products(prod_name, prod_imagen_url, prod_stock, prod_price)" .
               "VALUES (?, ?, ?, ?)"; // Añadimos prod_imagen_url

        $stmt = $conexion->prepare($sql);
        // s = string (name), s = string (imagen), i = int (stock), d = double (price)
        $stmt->bind_param("sssd", $prod_name, $prod_imagen_url, $prod_stock, $prod_price);

        try{
            if($stmt->execute()){
                $_SESSION['resultado'] = "Se ingreso producto con exito";
            }
            else{
                $_SESSION['resultado'] = "Error: " . $stmt->error;
            }
        }
        catch(mysqli_sql_exception $e){
                $_SESSION['resultado'] = "Error: " . $e->getMessage();
        }

        $stmt->close();
        $conexion->close();
    } else {
        $_SESSION['resultado'] = "Error: Todos los campos son obligatorios.";
    }

    header('Location: inventario.php');
    exit();
?>

