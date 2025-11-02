<?php
session_start();

//Verificar que se envió un ID y una cantidad
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prod_id']) && isset($_POST['cantidad'])) {
    
    $prod_id = $_POST['prod_id'];
    //Asegurarnos que la cantidad sea un entero
    $cantidad = (int)$_POST['cantidad'];

    //Verificar que el carrito exista y que el producto esté en él
    if (isset($_SESSION['carrito']) && array_key_exists($prod_id, $_SESSION['carrito'])) {
        
        if ($cantidad > 0) {
            //Actualizar la cantidad
            $_SESSION['carrito'][$prod_id] = $cantidad;
        } else {
            //Si la cantidad es 0 o menos, eliminar el producto
            unset($_SESSION['carrito'][$prod_id]);
        }
    }
}

//Redirigir siempre de vuelta a la página del carrito
header('Location: ../html/carrito.php');
exit();
?>