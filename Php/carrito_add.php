<?php
session_start();
// 1. Incluir la conexión a la BDD
require_once __DIR__ . '/../db_conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prod_id'])) {
    
    $prod_id = $_POST['prod_id'];

    // 2. Inicializar el carrito si no existe
    if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    // 3. Obtener la cantidad actual en el carrito
    $cantidad_en_carrito = 0;
    if (isset($_SESSION['carrito'][$prod_id])) {
        $cantidad_en_carrito = $_SESSION['carrito'][$prod_id];
    }

    // 4. Consultar el stock disponible en la BDD
    $sql = "SELECT prod_stock, prod_name FROM products WHERE prod_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $prod_id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $producto = $resultado->fetch_assoc();
        $stock_disponible = $producto['prod_stock'];

        // 5. Comparar la cantidad en carrito + 1 con el stock
        if (($cantidad_en_carrito + 1) <= $stock_disponible) {
            // Si hay stock, sumar 1
            $_SESSION['carrito'][$prod_id] = $cantidad_en_carrito + 1;
        } else {
            // Si no hay stock, crear un mensaje de error
            $_SESSION['mensaje_carrito'] = "No hay más stock disponible de " . htmlspecialchars($producto['prod_name']);
        }
    } else {
        // Producto no encontrado (raro, pero posible)
        $_SESSION['mensaje_carrito'] = "El producto que intentas agregar no existe.";
    }
    
    $stmt->close();
    $conexion->close();

}

// 6. Redirigir siempre de vuelta a la página principal
header('Location: ../index.php');
exit();
?>