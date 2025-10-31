<?php
session_start();

// Verificar que se envió un ID de producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prod_id'])) {
    
    $prod_id = $_POST['prod_id'];

    // Verificar que el carrito exista y que el producto esté en él
    if (isset($_SESSION['carrito']) && array_key_exists($prod_id, $_SESSION['carrito'])) {
        
        // Eliminar el producto del array del carrito
        unset($_SESSION['carrito'][$prod_id]);
    }
}

// Redirigir siempre de vuelta a la página del carrito
header('Location: ../html/carrito.php');
exit();
?>
