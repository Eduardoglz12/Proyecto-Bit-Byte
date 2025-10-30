<?php
// eliminar.php
session_start();
require_once __DIR__ . '/../db_conexion.php';

// Verificar que los datos lleguen por el método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Validar que el ID del producto fue enviado y es un número entero
    $prod_id = filter_input(INPUT_POST, 'prod_id', FILTER_VALIDATE_INT);

    if ($prod_id === false || $prod_id <= 0) {
        $_SESSION['resultado'] = "Error: ID de producto no válido.";
    } else {
        // 2. Preparar y ejecutar la consulta de eliminación (DELETE)
        $sql = "DELETE FROM products WHERE prod_id = ?";
        
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $prod_id);
        
        if ($stmt->execute()) {
            // Verificar si realmente se eliminó una fila
            if ($stmt->affected_rows > 0) {
                $_SESSION['resultado'] = "Producto eliminado con éxito.";
            } else {
                $_SESSION['resultado'] = "Error: No se encontró el producto a eliminar.";
            }
        } else {
            $_SESSION['resultado'] = "Error al ejecutar la eliminación: " . $stmt->error;
        }
        
        $stmt->close();
    }
} else {
    $_SESSION['resultado'] = "Acceso no permitido.";
}

$conexion->close();

// 3. Redirigir de vuelta a la página del inventario
header('Location: inventario.php');
exit();
?>