<?php
  session_start();
  require '../php/db_conexion.php';

  //Lógica de Sesión
  $textoSesion1 = "Iniciar sesion";
  $linkSesion1 = "inicioSesion.php";
  $textoSesion2 = "Registrarme";
  $linkSesion2 = "registro.php";

  if(isset($_SESSION['usr_user'])){
    $textoSesion1 = $_SESSION['usr_user'];
    $linkSesion1 = "perfil.php";
    $textoSesion2 = "Cerrar sesion";
    $linkSesion2 = "../php/cerrarSesion.php";
  }

  //Lógica del Carrito
  $totalItemsCarrito = 0;
  if (isset($_SESSION['carrito']) && is_array($_SESSION['carrito'])) {
      $totalItemsCarrito = array_sum($_SESSION['carrito']);
  }

  //Lógica para Cargar Productos del Carrito
  $productos_en_carrito = [];
  $gran_total = 0.0;

  if (!empty($_SESSION['carrito'])) {
      //Obtener los IDs de los productos del carrito
      $product_ids = array_keys($_SESSION['carrito']);
      
      //Preparar los placeholders para la consulta SQL (ej. ?,?,?)
      $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
      
      //Preparar la consulta
      $sql = "SELECT prod_id, prod_name, prod_imagen_url, prod_price, prod_stock FROM products WHERE prod_id IN ($placeholders)";
      $stmt = $conexion->prepare($sql);
      
      //Bindear los IDs
      // 'i' se repite por cada ID
      $types = str_repeat('i', count($product_ids));
      $stmt->bind_param($types, ...$product_ids);
      
      //Ejecutar y obtener resultados
      $stmt->execute();
      $resultado = $stmt->get_result();
      
      //Guardar los datos de los productos en un array para fácil acceso
      while ($producto = $resultado->fetch_assoc()) {
          $productos_en_carrito[$producto['prod_id']] = $producto;
      }
      $stmt->close();
  }
  $conexion->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bit&Byte - Carrito de Compras</title>
  <link rel="icon" href="../img/favicon.svg" type="image/svg+xml">
  <link rel="stylesheet" href="../Css/Inicio.css">
  <link rel="stylesheet" href="../Css/normalize.css">
  <link rel="preload" href="../Css/Inicio.css" as="style">
  <link rel="preload" href="../Css/normalize.css" as="style">
</head>

<body>
  
  <?php include 'header.php'; ?>
  
  <main>
    <div class="contenedor-principal">
      <div class="carrito-container full-width">
        <h1>Tu Carrito de Compras</h1>
        
        <?php if (empty($_SESSION['carrito'])): ?>
          <p class="carrito-vacio">Tu carrito está vacío. <a href="../index.php">¡Empieza a comprar!</a></p>
        <?php else: ?>
          
          <table class="carrito-tabla">
            <thead>
              <tr>
                <th>Producto</th>
                <th>Precio Unitario</th>
                <th>Cantidad</th>
                <th>Total</th>
                <th>Eliminar</th>
              </tr>
            </thead>
            <tbody>
              <?php 
                // Iterar sobre los items en la SESIÓN
                foreach ($_SESSION['carrito'] as $prod_id => $cantidad):
                  // Verificar que el producto fue encontrado en la BDD
                  if (isset($productos_en_carrito[$prod_id])):
                    $producto = $productos_en_carrito[$prod_id];
                    $total_linea = $producto['prod_price'] * $cantidad;
                    $gran_total += $total_linea;
              ?>
                <tr>
                  <!-- Celda Producto (Imagen + Nombre) -->
                  <td class="producto-celda">
                      <img src="../<?= htmlspecialchars($producto['prod_imagen_url']); ?>" 
                      alt="<?= htmlspecialchars($producto['prod_name']); ?>" class="carrito-img">
                      <span><?= htmlspecialchars($producto['prod_name']); ?></span>
                  </td>
                  
                  <!-- Celda Precio Unitario -->
                  <td>$<?php echo number_format($producto['prod_price'], 2); ?></td>
                  
                  <!-- Celda Cantidad (Spin Box) -->
                  <td class="cantidad-celda">
                    <form action="../php/carrito_update.php" method="POST" class="form-cantidad">
                      <input type="hidden" name="prod_id" value="<?php echo $prod_id; ?>">
                      <input type="number" name="cantidad" value="<?php echo $cantidad; ?>" 
                             min="1" max="<?php echo $producto['prod_stock']; ?>" class="spin-box"
                             onchange="this.form.submit()">
                      
                      <button type="submit" class="btn-update">Actualizar</button>
                    </form>
                  </td>
                  
                  <!-- Celda Total por Línea -->
                  <td>$<?php echo number_format($total_linea, 2); ?></td>
                  
                  <!-- Celda Eliminar (Botón) -->
                  <td class="eliminar-celda">
                    <form action="../php/carrito_remove.php" method="POST">
                      <input type="hidden" name="prod_id" value="<?php echo $prod_id; ?>">
                      <button type="submit" class="btn-remove" title="Eliminar producto">
                        <!-- Icono de "X" -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"></path></svg>
                      </button>
                    </form>
                  </td>
                </tr>
              <?php 
                  endif; // Fin del if isset
                endforeach; // Fin del bucle foreach 
              ?>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="3" class="carrito-total-label">Total:</td>
                <td colspan="2" class="carrito-total-monto">$<?php echo number_format($gran_total, 2); ?></td>
              </tr>
            </tfoot>
          </table>

          <div class="checkout-seccion">
            <form action="comprar.php">
              <button type="submit" class="btn-checkout">Continuar con el pago</button>
            </form>
          </div>

        <?php endif; // Fin del else (carrito no vacío) ?>
        
      </div>
    </div>
  </main>
  
  <footer>
    Derechos Reservados ©. Bit&Byte
  </footer>
</body>
</html>

