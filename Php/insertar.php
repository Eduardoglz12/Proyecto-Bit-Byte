<?php
    session_start();

    // Variables base de datos 
    $dbhost = "localhost";
    $dbuser = "root";
    $dbpass = "";
    $dbname = "bitandbyte";
    $dbport = "3306";

    $prod_name = $_POST['prod_name'];
    $prod_stock = $_POST['prod_stock'];
    $prod_price = $_POST['prod_price'];

    if(!empty($prod_name) && !empty($prod_stock) && !empty($prod_price)){
        $conexion = new mysqli($dbhost, $dbuser, $dbpass, $dbname, $dbport);

        $sql = "INSERT INTO products(prod_name, prod_stock, prod_price)" .
            "VALUES (?, ?, ?)";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sid", $prod_name, $prod_stock, $prod_price);

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
    }

    header('Location: inventario.php');
?>