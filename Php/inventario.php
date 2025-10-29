<?php
    session_start();
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
    $sql_tabla = "SELECT prod_name, prod_imagen_url, prod_spec_url, prod_stock, prod_price FROM products";
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
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>URL Imagen (Tarjeta)</th>
                    <th>URL Especificaciones (Grande)</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <form action="insertar.php" method="post">
                        <td><input type="text" name="prod_name" required></td>
                        <td><input type="text" name="prod_imagen_url" placeholder="ej: img/art1.webp" required></td>
                        <td><input type="text" name="prod_spec_url" placeholder="ej: img/specs/art1_specs.webp" required></td>
                        <td><input type="number" name="prod_stock" required></td>
                        <td><input type="text" name="prod_price" placeholder="ej: 2199.00" required></td>
                        <td><button type="submit">Ingresar</button></td>
                    </form>
                </tr>
            </tbody>
        </table>

        <table>
            <caption>Editar producto</caption>
            </table>

        <table>
            <caption>Inventario Actual</caption>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>URL Imagen</th>
                    <th>URL Specs</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado_tabla->num_rows > 0): ?>
                    <?php while($fila = $resultado_tabla->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fila['prod_name']); ?></td>
                            <td><?php echo htmlspecialchars($fila['prod_imagen_url']); ?></td>
                            <td><?php echo htmlspecialchars($fila['prod_spec_url']); ?></td>
                            <td><?php echo htmlspecialchars($fila['prod_stock']); ?></td>
                            <td>$<?php echo number_format($fila['prod_price'], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No hay productos en el inventario.</td>
                    </tr>
                <?php endif; ?>
                <?php
                    $stmt_select->close();
                    $stmt_tabla->close();
                    $conexion->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>