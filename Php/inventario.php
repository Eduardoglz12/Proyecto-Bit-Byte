<?php
    session_start();
    // 1. Usamos la conexión centralizada
    require_once __DIR__ . '/../db_conexion.php';

    $resultado = "";

    if(isset($_SESSION['resultado'])){
        $resultado = $_SESSION['resultado'];
        unset($_SESSION['resultado']);
    }

    // 2. La variable $conexion ya viene de db_conexion.php
    
    // Consulta para el <select> de editar
    $sql_select = "SELECT prod_id, prod_name FROM products";
    $stmt_select = $conexion->prepare($sql_select);
    $stmt_select->execute();
    $resultado_select = $stmt_select->get_result();

    // Consulta para la tabla de inventario
    $sql_tabla = "SELECT prod_name, prod_imagen_url, prod_stock, prod_price FROM products";
    $stmt_tabla = $conexion->prepare($sql_tabla);
    $stmt_tabla->execute();
    $resultado_tabla = $stmt_tabla->get_result();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/inventario.css">
    <link rel="preload" href="../css/inventario.css" as="style">
    <title>Inventario</title>
</head>
<body>
    <div class="cont-principal">
        <?php if(!empty($resultado)): ?>
            <p class="resultado"><?php echo htmlspecialchars($resultado); ?></p>
        <?php endif; ?>
        
        <table>
            <caption>Producto nuevo</caption>
            <tr>
                <th>Producto</th>
                <th>URL Imagen (ej: img/art1.webp)</th> <!-- Campo añadido -->
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Acción</th>
            </tr>
            <tr>
                <form action="insertar.php" method="post">
                    <td><input type="text" name="prod_name" required></td>
                    <td><input type="text" name="prod_imagen_url" required></td> <!-- Campo añadido -->
                    <td><input type="number" name="prod_stock" required></td>
                    <td><input type="text" name="prod_price" required></td>
                    <td><button type="submit">Ingresar</button></td>
                </form>
            </tr>
        </table>

        <table>
            <caption>Editar producto</caption>
            <tr>
                <form action="editar.php" method="GET"> <!-- Asumiendo que crearás un editar.php -->
                    <td colspan="3">
                        <select name="prod_id" id="">
                            <option value="">-- Seleccione un producto --</option>
                            <?php if($resultado_select->num_rows > 0): ?>
                                <?php while($fila = $resultado_select->fetch_assoc()):?>
                                    <option value="<?php echo $fila['prod_id']; ?>">
                                        <?php echo htmlspecialchars($fila['prod_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </td>
                    <td>
                        <button type="submit">Elegir</button>
                    </td>
                </form>
            </tr>
            <!-- Aquí iría el formulario de edición, que se llenaría con PHP -->
        </table>

        <table>
            <caption>Inventario Actual</caption>
            <tr>
                <th>Producto</th>
                <th>Imagen (URL)</th>
                <th>Cantidad</th>
                <th>Precio</th>
            </tr>
            <?php if ($resultado_tabla->num_rows > 0): ?>
                <?php while($fila = $resultado_tabla->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fila['prod_name']); ?></td>
                        <td><?php echo htmlspecialchars($fila['prod_imagen_url']); ?></td>
                        <td><?php echo htmlspecialchars($fila['prod_stock']); ?></td>
                        <td>$<?php echo number_format($fila['prod_price'], 2); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No hay productos en el inventario.</td>
                </tr>
            <?php endif; ?>
            <?php
                $stmt_select->close();
                $stmt_tabla->close();
                $conexion->close();
            ?>
        </table>
    </div>
</body>
</html>

