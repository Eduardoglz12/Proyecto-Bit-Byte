<?php
session_start();
require '../php/db_conexion.php';

$resultado = $_SESSION['resultado'] ?? null;
if ($resultado) {
    unset($_SESSION['resultado']);
}

//LÓGICA PARA CARGAR DATOS DEL PRODUCTO A EDITAR
$producto_a_editar = null;
if (isset($_GET['edit_id']) && !empty($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $sql_edit = "SELECT prod_id, prod_name, prod_imagen_url, prod_spec_url, prod_stock, prod_price FROM products WHERE prod_id = ?";
    $stmt_edit = $conexion->prepare($sql_edit);
    $stmt_edit->bind_param("i", $edit_id);
    $stmt_edit->execute();
    $producto_a_editar = $stmt_edit->get_result()->fetch_assoc();
    $stmt_edit->close();
}

//CONSULTAS PARA LOS FORMULARIOS Y LA TABLA
// Consulta para el <select> de editar
$sql_select = "SELECT prod_id, prod_name FROM products";
$stmt_select = $conexion->prepare($sql_select);
$stmt_select->execute();
$resultado_select = $stmt_select->get_result();

// Consulta para la tabla de inventario
$sql_tabla = "SELECT prod_id, prod_name, prod_imagen_url, prod_spec_url, prod_stock, prod_price FROM products";
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
    <link rel="icon" href="../img/favicon.svg" type="image/svg+xml">
    <title>Inventario - Bit&Byte</title>
</head>
<body>
    <div class="cont-principal">

        <a href="../index.php" class="btn-home">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"></path></svg>
            <span>Volver a la Tienda</span>
        </a>

        <?php if (!empty($resultado)): ?>
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
                    <form action="../php/insertar.php" method="post">
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
            <tbody>
                <tr>
                    <form action="inventario.php" method="get">
                        <td colspan="5">
                            <label for="edit_id">Selecciona un producto para editar:</label>
                            <select name="edit_id" id="edit_id" required>
                                <option value="">-- Elige un producto --</option>
                                <?php while ($fila_select = $resultado_select->fetch_assoc()): ?>
                                    <option value="<?php echo $fila_select['prod_id']; ?>">
                                        <?php echo htmlspecialchars($fila_select['prod_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </td>
                        <td><button type="submit">Cargar Datos</button></td>
                    </form>
                </tr>

                <?php if ($producto_a_editar): ?>
                    <tr>
                        <form action="../php/actualizar.php" method="post">
                            <input type="hidden" name="prod_id" value="<?php echo $producto_a_editar['prod_id']; ?>">
                            <td><input type="text" name="prod_name" value="<?php echo htmlspecialchars($producto_a_editar['prod_name']); ?>" required></td>
                            <td><input type="text" name="prod_imagen_url" value="<?php echo htmlspecialchars($producto_a_editar['prod_imagen_url']); ?>" required></td>
                            <td><input type="text" name="prod_spec_url" value="<?php echo htmlspecialchars($producto_a_editar['prod_spec_url']); ?>" required></td>
                            <td><input type="number" name="prod_stock" value="<?php echo htmlspecialchars($producto_a_editar['prod_stock']); ?>" required></td>
                            <td><input type="text" name="prod_price" value="<?php echo htmlspecialchars($producto_a_editar['prod_price']); ?>" required></td>
                            <td><button type="submit">Guardar Cambios</button></td>
                        </form>
                    </tr>
                <?php endif; ?>
            </tbody>
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
                    <th>Acción</th> </tr>
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
                            
                            <td>
                                <form action="../php/eliminar.php" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este producto?');">
                                    <input type="hidden" name="prod_id" value="<?php echo $fila['prod_id']; ?>">
                                    <button type="submit" class="btn-eliminar">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No hay productos en el inventario.</td>
                    </tr>
                <?php endif; ?>
                <?php
                    // Cerrar conexiones al final
                    $stmt_select->close();
                    $stmt_tabla->close();
                    $conexion->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>