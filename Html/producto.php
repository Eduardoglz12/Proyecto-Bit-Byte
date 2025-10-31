<?php
  session_start();
  require '../php/db_conexion.php'; // Incluimos la conexión

  // --- Lógica de Sesión de Usuario ---
  $textoSesion1 = "Iniciar sesion";
  $linkSesion1 = "inicioSesion.php";
  $textoSesion2 = "Registrarme";
  $linkSesion2 = "registro.php";

  if(isset($_SESSION['usr_user'])){
    $textoSesion1 = $_SESSION['usr_user'];
    $linkSesion1 = "#";
    $textoSesion2 = "Cerrar sesion";
    $linkSesion2 = "../php/cerrarSesion.php";
  }

  // --- Lógica del Carrito (para el contador) ---
  $totalItemsCarrito = 0;
  if (isset($_SESSION['carrito']) && is_array($_SESSION['carrito'])) {
      $totalItemsCarrito = array_sum($_SESSION['carrito']);
  }

  // --- Lógica para Cargar el Producto Específico ---
  $producto = null;
  // 1. Verificar si se pasó una ID por la URL
  if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $prod_id = $_GET['id'];

    // 2. Preparar la consulta para evitar inyección SQL
    $sql = "SELECT prod_id, prod_name, prod_imagen_url, prod_spec_url, prod_price, prod_stock FROM products WHERE prod_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $prod_id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    // 3. Si se encontró el producto, guardar sus datos
    if ($resultado->num_rows === 1) {
      $producto = $resultado->fetch_assoc();
    }
    $stmt->close();
  }
  $conexion->close();

  // Si no se encontró el producto, podríamos redirigir o mostrar un error.
  // Por ahora, el HTML de abajo manejará el caso de que $producto sea null.
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $producto ? htmlspecialchars($producto['prod_name']) : 'Producto no encontrado'; ?> - Bit&Byte</title>
  
  <link rel="icon" href="../img/favicon.svg" type="image/svg+xml">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../CSS/Inicio.css">
  <link rel="stylesheet" href="../CSS/normalize.css">
</head>

<body>

    <?php include 'header.php'; ?>
  
    <main>
        <div class="contenedor-principal">
            <?php if ($producto): // Si el producto se encontró en la BDD, muestra esto ?>
                <div class="detalle-producto-container">
                    
                    <div class="detalle-imagen-principal">
                        <img src="../<?php echo htmlspecialchars($producto['prod_imagen_url']); ?>" 
                          alt="<?php echo htmlspecialchars($producto['prod_name']); ?>">
                    </div>

                    <div class="detalle-info-compra">
                        <h1><?php echo htmlspecialchars($producto['prod_name']); ?></h1>
                        <p class="detalle-precio">$<?php echo number_format($producto['prod_price'], 2); ?></p>
                        
                        <form action="../php/carrito_add.php" method="POST" class="form-detalle-carrito">
                            <input type="hidden" name="prod_id" value="<?php echo $producto['prod_id']; ?>">
                            
                            <div class="control-cantidad">
                                <label for="cantidad">Cantidad:</label>
                                <input type="number" id="cantidad" name="cantidad" value="1" 
                                    min="1" max="<?php echo $producto['prod_stock']; ?>" class="spin-box">
                            </div>
                            
                            <button type="submit" class="btn-agregar-carrito">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                                <span>Agregar al Carrito</span>
                            </button>
                        </form>
                        <p class="detalle-stock">Stock disponible: <?php echo $producto['prod_stock']; ?> unidades</p>
                    </div>

                    <div class="detalle-especificaciones">
                        <h2>Especificaciones</h2>
                        <img src="../<?php echo htmlspecialchars($producto['prod_spec_url']); ?>" 
                          alt="Especificaciones de <?php echo htmlspecialchars($producto['prod_name']); ?>">
                    </div>

                </div>
            <?php else: // Si no se encontró el producto, muestra esto ?>
                <div class="producto-no-encontrado">
                    <h1>Producto no encontrado</h1>
                    <p>Lo sentimos, el producto que buscas no existe o no está disponible.</p>
                    <a href="../index.php" class="btn-checkout">Volver a la tienda</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
  
  <footer>
    Derechos Reservados ©. Bit&Byte
  </footer>
</body>
</html>