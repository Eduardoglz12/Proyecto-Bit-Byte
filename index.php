<?php
  session_start();
  // Incluimos la conexión a la BDD
   require 'php/db_conexion.php';

  //Lógica de Sesión
$textoSesion1 = "Iniciar sesión";
$linkSesion1 = "html/inicioSesion.php";
$textoSesion2 = "Registrarme";
$linkSesion2 = "html/registro.php";

    // --- LÓGICA PARA EL HEADER ---
if (isset($_SESSION['usr_user'])) {
    $textoSesion1 = $_SESSION['usr_user'];
    $linkSesion1 = "html/perfil.php";
    $textoSesion2 = "Cerrar sesión";
    $linkSesion2 = "php/cerrarSesion.php";
    }
    
  //Lógica del Carrito (para el contador)
  $totalItemsCarrito = 0;
  if (isset($_SESSION['carrito']) && is_array($_SESSION['carrito'])) {
      $totalItemsCarrito = array_sum($_SESSION['carrito']);
  }

  //Lógica para Mensaje de Carrito
  $mensaje_carrito = "";
  if (isset($_SESSION['mensaje_carrito'])) {
      $mensaje_carrito = $_SESSION['mensaje_carrito'];
      // Borrar el mensaje para que no se muestre de nuevo
      unset($_SESSION['mensaje_carrito']);
  }

  $busqueda = trim($_GET['busqueda'] ?? '');

  //Preparar la consulta a la base de datos
  if (!empty($busqueda)) {
      // Si hay un término de búsqueda, filtramos los productos
      $titulo_pagina = "Resultados para: '" . htmlspecialchars($busqueda) . "'";
      $sql = "SELECT * FROM products WHERE prod_name LIKE ?";
      $stmt = $conexion->prepare($sql);
      
      $termino_busqueda = "%" . $busqueda . "%";
      $stmt->bind_param("s", $termino_busqueda);
      
  } else {
      $titulo_pagina = "Nuestros Productos";
      $sql = "SELECT * FROM products";
      $stmt = $conexion->prepare($sql);
  }

  $stmt->execute();
  $resultado_productos = $stmt->get_result();
  $stmt->close();
  $conexion->close();

?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bit&Byte</title>
  <link rel="icon" href="img/favicon.svg" type="image/svg+xml">
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">

  <link rel="preload" href="Css/normalize.css" as="style">
  <link rel="preload" href="Css/Inicio.css" as="style">
  <link rel="stylesheet" href="Css/normalize.css">
  <link rel="stylesheet" href="Css/Inicio.css">
  
  <link rel="stylesheet" type="text/Css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
  <link rel="stylesheet" type="text/Css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
  
</head>

<body>

  <header>
    <div class="barra-negra">
        <div class="ba-contenedor">
            <ul>
                <li><a href="#">Nosotros</a></li>
                <li>|</li>
                <li><a href="#">Blog</a></li>
                <li>|</li>
                <li><a href="#">Contacto</a></li>
            </ul>
        </div>
        <div class="ba-contenedor central">
            <ul>
                <li><a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.04c-5.5 0-10 4.49-10 10s4.5 10 10 10 10-4.49 10-10-4.5-10-10-10zm3.5 10.42h-2.21v6.54h-2.59v-6.54h-1.39v-2.21h1.39v-1.57c0-1.1 .42-2.58 2.58-2.58h1.8v2.21h-1.09c-.33 0-.6.25-.6.67v1.27h1.69l-.22 2.21z"></path></svg></a></li>
                <li><a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M21.58 7.19c-.23-.86-.91-1.54-1.77-1.77C18.25 5 12 5 12 5s-6.25 0-7.81.42c-.86.23-1.54.91-1.77 1.77C2 8.75 2 12 2 12s0 3.25.42 4.81c.23.86.91 1.54 1.77 1.77C5.75 19 12 19 12 19s6.25 0 7.81-.42c.86-.23 1.54-.91 1.77-1.77C22 15.25 22 12 22 12s0-3.25-.42-4.81zM10 14.65v-5.3L15 12l-5 2.65z"></path></svg></a></li>
                <li><a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"></path></svg></a></li>
                <li><a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2c-2.72 0-3.05.01-4.12.06-1.06.05-1.79.22-2.42.46-.65.25-1.23.58-1.77 1.14-.54.55-.89 1.12-1.14 1.77-.24.63-.41 1.36-.46 2.42C2.01 8.95 2 9.28 2 12s.01 3.05.06 4.12c.05 1.06.22 1.79.46 2.42.25.65.58 1.23 1.14 1.77.55.54 1.12.89 1.77 1.14.63.24 1.36.41 2.42.46 1.07.05 1.4.06 4.12.06s3.05-.01 4.12-.06c1.06-.05 1.79-.22 2.42-.46.65-.25 1.23-.58 1.77-1.14.54-.55-.89-1.12-1.14-1.77.24-.63.41 1.36.46 2.42.05-1.07.06-1.4.06-4.12s-.01-3.05-.06-4.12c-.05-1.06-.22-1.79-.46-2.42-.25-.65-.58-1.23-1.14-1.77-.55-.54-1.12-.89-1.77-1.14-.63-.24-1.36-.41-2.42-.46C15.05 2.01 14.72 2 12 2zm0 1.8c2.67 0 2.98.01 4.04.06 1-.05 1.52.19 1.85.32.42.16.72.39.99.66.27.27.5.57.66.99.13.33.37.85.32 1.85.05 1.06.06 1.37.06 4.04s-.01 2.98-.06 4.04c-.05 1 .19 1.52-.32 1.85-.16.42-.39.72-.66.99-.27.27-.57.5-.99.66-.33.13-.85.37-1.85.32-1.06.05-1.37.06-4.04.06s-2.98-.01-4.04-.06c-1-.05-1.52-.19-1.85.32-.42-.16-.72-.39-.99-.66-.27-.27-.5-.57-.66-.99-.13-.33-.37-.85-.32-1.85-.05-1.06-.06-1.37-.06-4.04s.01-2.98.06-4.04c.05-1-.19-1.52.32-1.85.16-.42.39-.72.66.99.27-.27.57-.5.99-.66.33-.13.85-.37 1.85-.32C9.02 3.81 9.33 3.8 12 3.8zm0 5.32c-1.93 0-3.5 1.57-3.5 3.5s1.57 3.5 3.5 3.5 3.5-1.57 3.5-3.5-1.57-3.5-3.5-3.5zm0 5.2c-.94 0-1.7-.76-1.7-1.7s.76-1.7 1.7-1.7 1.7.76 1.7 1.7-.76 1.7-1.7 1.7zm6.22-8.3c-.55 0-1 .45-1 1s.45 1 1 1 1-.45 1-1-.45-1-1-1z"></path></svg></a></li>
                <li><a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12.53.02C13.84 0 15.14.01 16.44 0c.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-2.43.05-4.84-.94-6.46-2.93-1.62-2-2.11-4.88-1.13-7.12.82-1.84 2.62-3.14 4.5-3.48.64-.11 1.28-.21 1.92-.23.01-2.5.01-5 .01-7.5 0-1.53.01-3.05.01-4.57.17-.01.33-.02.5-.02.43 0 .86-.01 1.29 0 .28.01.52.12.7.33.21.24.3.52.32.82.04 1.13.02 2.26.02 3.39.01 2.25.01 4.5.01 6.75v.27c-.01 1.39.04 2.78.02 4.17-.01 1.14-.3 2.24-.9 3.17-.89 1.39-2.31 2.24-3.92 2.22-2.28-.03-4.35-1.63-4.96-3.87-.33-1.19-.24-2.48.24-3.66.52-1.29 1.48-2.29 2.66-2.92.49-.26 1-.46 1.53-.61.02-2.49.01-4.97.01-7.46 0-.79-.06-1.58.02-2.37.09-.9.42-1.75 1.05-2.45.64-.71 1.42-1.21 2.28-1.5.8-.28 1.63-.4 2.48-.41.56-.01 1.12-.02 1.68-.02z"></path></svg></a></li>
                <li>|</li>
                <li><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"></path></svg> <span>871-661-3951</span></li>
                <li>|</li>
                <li><a href="#">Ayuda</a></li>
            </ul>
        </div>
        <div class="ba-contenedor">
            <ul>
                <li><a href="<?php echo htmlspecialchars($linkSesion1); ?>"><?php echo htmlspecialchars($textoSesion1); ?></a></li>
                <li>|</li>
                <li><a href="<?php echo htmlspecialchars($linkSesion2); ?>"><?php echo htmlspecialchars($textoSesion2); ?></a></li>
            </ul>
        </div>
    </div>
    <div class="barra-azul">
        <div class="ba-contenedor">
            <a href="index.php">
                <svg width="200" height="80" viewBox="0 0 200 80" xmlns="http://www.w3.org/2000/svg">
                    <g transform="translate(5, 15)"><rect x="0" y="0" width="10" height="50" fill="#3B82F6" /><rect x="10" y="0" width="10" height="10" fill="#3B82F6" /><rect x="20" y="10" width="10" height="10" fill="#93C5FD" /><rect x="10" y="20" width="10" height="10" fill="#3B82F6" /><rect x="20" y="30" width="10" height="10" fill="#93C5FD" /><rect x="10" y="40" width="10" height="10" fill="#3B82F6" /></g>
                    <text x="55" y="52" font-family="Poppins, sans-serif" font-size="28" font-weight="600" fill="#93C5FD" class="dark:fill-white">Bit<tspan fill="#3B82F6">&</tspan>Byte</text>
                </svg>
            </a>
        </div>
        <div class="ba-contenedor">
            <ul>
                <li><a href="index.php"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"></path></svg></a></li>
                <li>|</li>
                <li><a href="#">PRODUCTOS</a></li>
                <li>|</li>
                <li><a href="#">TIENDAS</a></li>
                <li>|</li>
                <li><a href="#">PRIDE</a></li>
                <li>|</li>
                <li><a href="#">CONFIGURAR PC</a></li>
            </ul>
        </div>
        <div class="search-form">
            <ul>
                <li>
                    <form action="index.php" method="GET">
                        <input type="text" name="busqueda" placeholder="Buscar...">
                        <button type="submit" class="botonBuscar">Buscar</button>
                    </form>
                </li>
                <li class="carrito">
                    <a href="html/carrito.php">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"></path></svg>
                        <span><?php echo $totalItemsCarrito; ?></span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
  </header>

  <main>
    <div class="contenedor-principal">
            
            <aside class="columna1">
              <ul>
                <li class="li-titulo">CATEGORIAS</li>
                <li><a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 17H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2Z"></path><path d="M12 11v-1"></path><path d="M7 11v-1"></path><path d="M17 11v-1"></path></svg><span> Accesorios</span></a></li>
                <li><a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="20" x="5" y="2" rx="2" ry="2"></rect><path d="M12 18h.01"></path></svg><span> Computadoras</span></a></li>
                <li><a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"></rect><path d="M15 9h-3a3 3 0 1 0 0 6h3"></path><path d="M12 7V5"></path><path d="M12 19v-2"></path><path d="M9 12H7"></path><path d="M17 12h-2"></path></svg><span> Componentes</span></a></li>
                <li><a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="3" rx="2"></rect><path d="M8 21h8"></path><path d="M12 17v4"></path></svg><span> Monitores</span></a></li>
                <li><a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14.22a7.5 7.53 0 0 0-1.42-4.14A7.47 7.47 0 0 0 12 8a7.5 7.5 0 0 0-6 11.23"></path><path d="M16.5 10.5a4.5 4.5 0 1 0-9 0"></path><path d="M12 2v6"></path><path d="M12 19v3"></path><path d="M18.93 18.93l-2.12-2.12"></path><path d="M7.19 7.19 5.07 5.07"></path></svg><span> Audio</span></a></li>
              </ul>
            </aside>
            
            <div class="columna2">

            <article class="noticias">
                <div class="hero-slider">
                    <div><img src="img/banner1.webp" alt="Banner Promoción 1"></div>
                    <div><img src="img/banner2.webp" alt="Banner Promoción 2"></div>
                    <div><img src="img/banner3.webp" alt="Banner Promoción 3"></div>
                    <div><img src="img/banner4.webp" alt="Banner Promoción 4"></div>
                    <div><img src="img/banner5.webp" alt="Banner Promoción 5"></div>
                </div>
            </article>

              <?php if (!empty($mensaje_carrito)): ?>
                <div class="mensaje-carrito-error">
                  <?php echo $mensaje_carrito; ?>
                </div>
              <?php endif; ?>
                
                <h2><?php echo $titulo_pagina; ?></h2>

                <div class="grid-productos">
                    <?php if ($resultado_productos->num_rows > 0): ?>
                        <?php while($producto = $resultado_productos->fetch_assoc()): ?>
                            <div class="producto">
                                <a href="html/producto.php?id=<?php echo $producto['prod_id']; ?>">
                                    <img src="<?php echo htmlspecialchars($producto['prod_imagen_url']); ?>" alt="<?php echo htmlspecialchars($producto['prod_name']); ?>">
                                    <p class="descripcion"><?php echo htmlspecialchars($producto['prod_name']); ?></p>
                                </a>
                                <p class="precio">$<?php echo number_format($producto['prod_price'], 2); ?></p>
                                <form action="php/carrito_add.php" method="POST" class="form-carrito">
                                    <input type="hidden" name="prod_id" value="<?php echo $producto['prod_id']; ?>">
                                    <input type="hidden" name="cantidad" value="1">
                                    <button type="submit" class="btn-agregar-carrito">
                                        <span>Agregar</span>
                                    </button>
                                </form>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="carrito-vacio">No se encontraron productos que coincidan con tu búsqueda.</p>
                    <?php endif; ?>
                </div>
            </div> </div> </main>
    
    <footer>
        Derechos Reservados © Bit&Byte
    </footer>
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script src="js/main.js"></script>

</body>
</html>