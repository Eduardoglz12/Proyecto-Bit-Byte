<?php
    session_start();
    // Variables base de datos 
    $dbhost = "localhost";
    $dbuser = "root";
    $dbpass = "";
    $dbname = "bitandbyte";
    $dbport = "3306";

    $resultado = "";

    if(isset($_SESSION['resultado'])){
        $resultado = $_SESSION['resultado'];
        unset($_SESSION['resultado']);
    }

    $conexion = new mysqli($dbhost, $dbuser, $dbpass, $dbname, $dbport);

    $sql = "SELECT * FROM products";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();

    $sql = $stmt->get_result();

    // Variables de edicion
    $prod_name = "";
    $prod_amount = "";
    $prod_price = "";
?>

<!DOCTYPE html>
<html lang="en">
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
            <p class="resultado"><?php echo $resultado ?></p>
        <?php endif; ?>
        <table>
            <caption>Producto nuevo</caption>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio</th>
            </tr>
            <tr>
                <form action="insertar.php" method="post">
                    <th><input type="text" name="prod_name"></th>
                    <th><input type="text" name="prod_stock"></th>
                    <th><input type="text" name="prod_price"></th>
                    <th><button type="submit">Ingresar</button></th>
                </form>
            </tr>
        </table>

        <table>
            <caption>Editar producto</caption>
            <tr>
                <form action="">
                    <td colspan="3">
                        <select name="productos" id="">
                            <?php if(!empty($sql)): ?>
                                <?php while($fila = $sql->fetch_assoc()):?>
                                    <option value="<?php echo$fila['prod_name']; ?>">
                                        <?php echo$fila['prod_name']; ?>
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
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th><button type="submit">Eliminar</button></th>
            </tr>
            <tr>
                <form action="" method="post">
                    <th><input type="text" name="prod_name"></th>
                    <th><input type="text" name="prod_stock"></th>
                    <th><input type="text" name="prod_price"></th>
                    <th><button type="submit">Editar</button></th>
                </form>
            </tr>
        </table>

        <table>
            <caption>Inventario</caption>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio</th>
            </tr>
            <tr>
                <td>RAMSGYHUSDGKHJSDKJGHSDFGJKL,HSDFHJK</td>
                <td>4</td>
                <td>$12,000</td>
            </tr>
            <tr>
                <td>RAMSGYHHSDFGJKL,HSDFHJK</td>
                <td>4</td>
                <td>$12,000,894</td>
            </tr>
            <tr>
                <td>RAMSGYHHSDFGJKL,HSDFHJKWEREW</td>
                <td>41</td>
                <td>$12,000,894</td>
            </tr>
            <tr>
                <td>RAMSGYHHSDFGJKLFHJK</td>
                <td>56</td>
                <td>$12,00</td>
            </tr>
        </table>
    </div>
</body>
</html>