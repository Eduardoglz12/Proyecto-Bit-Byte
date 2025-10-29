<?php
session_start();
// 1. Incluir la conexión a la BDD para validar el stock
require_once __DIR__ . '/../db_conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prod_id'])) {
    
    $prod_id = (int)$_POST['prod_id'];
    // 2. Si se envía una cantidad (desde producto.php), úsala. Si no (desde index.php), usa 1.
    $cantidad_a_agregar = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1;

    // 3. Inicializar el carrito si no existe
    if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    // 4. Obtener la cantidad que ya está en el carrito para este producto
    $cantidad_en_carrito = isset($_SESSION['carrito'][$prod_id]) ? $_SESSION['carrito'][$prod_id] : 0;

    // 5. Consultar el stock disponible en la BDD
    $sql = "SELECT prod_stock, prod_name FROM products WHERE prod_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $prod_id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $producto = $resultado->fetch_assoc();
        $stock_disponible = $producto['prod_stock'];

        // 6. VALIDACIÓN: Comprobar si la cantidad deseada (lo que hay + lo que se agrega) supera el stock
        if (($cantidad_en_carrito + $cantidad_a_agregar) <= $stock_disponible) {
            // Si hay stock, se suma la nueva cantidad
            $_SESSION['carrito'][$prod_id] = $cantidad_en_carrito + $cantidad_a_agregar;
        } else {
            // Si no hay stock, se crea un mensaje de error y se redirige de vuelta
            $_SESSION['mensaje_carrito'] = "No hay suficiente stock para '" . htmlspecialchars($producto['prod_name']) . "'. Solo quedan " . $stock_disponible . " unidades.";
            // Redirigimos a la página anterior para mostrar el error
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }
    } else {
        // Producto no encontrado
        $_SESSION['mensaje_carrito'] = "El producto que intentas agregar no existe.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
    
    $stmt->close();
    $conexion->close();

}

// 7. Redirigir a la página del CARRITO para que el usuario vea el producto añadido
header('Location: ../carrito.php');
exit();
?>