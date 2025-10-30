<?php
// actualizar.php
session_start();
require_once __DIR__ . '/../db_conexion.php';

// Verificar que los datos lleguen por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Recoger y sanear los datos
    $prod_id = $_POST['prod_id'] ?? null;
    $prod_name = trim($_POST['prod_name'] ?? '');
    $prod_imagen_url = trim($_POST['prod_imagen_url'] ?? '');
    $prod_spec_url = trim($_POST['prod_spec_url'] ?? '');
    $prod_stock = filter_input(INPUT_POST, 'prod_stock', FILTER_VALIDATE_INT);
    $prod_price = filter_input(INPUT_POST, 'prod_price', FILTER_VALIDATE_FLOAT);

    // 2. Validar que los datos no estén vacíos
    if (empty($prod_id) || empty($prod_name) || empty($prod_imagen_url) || empty($prod_spec_url) || $prod_stock === false || $prod_price === false) {
        $_SESSION['resultado'] = "Error: Todos los campos son obligatorios y deben ser válidos.";
    } else {
        // 3. Preparar y ejecutar la consulta de actualización (UPDATE)
        $sql = "UPDATE products 
                SET prod_name = ?, prod_imagen_url = ?, prod_spec_url = ?, prod_stock = ?, prod_price = ? 
                WHERE prod_id = ?";
        
        $stmt = $conexion->prepare($sql);
        
        // sssidi -> s: string, i: integer, d: double (float)
        $stmt->bind_param("sssidi", $prod_name, $prod_imagen_url, $prod_spec_url, $prod_stock, $prod_price, $prod_id);
        
        if ($stmt->execute()) {
            $_SESSION['resultado'] = "Producto actualizado con éxito.";
        } else {
            $_SESSION['resultado'] = "Error al actualizar el producto: " . $stmt->error;
        }
        
        $stmt->close();
    }
} else {
    $_SESSION['resultado'] = "Acceso no válido.";
}

$conexion->close();

// 4. Redirigir de vuelta a la página del inventario
header('Location: inventario.php');
exit();
?>