-- ===================================================================
CREATE TABLE `users` (
  `usr_id` INT AUTO_INCREMENT PRIMARY KEY,
  `usr_user` VARCHAR(50) NOT NULL UNIQUE,
  `usr_password` VARCHAR(255) NOT NULL,
  `usr_email` VARCHAR(255) NULL DEFAULT NULL,
  `usr_nombre_completo` VARCHAR(255) NULL DEFAULT NULL,
  `usr_telefono` VARCHAR(20) NULL DEFAULT NULL,
  `usr_calle` VARCHAR(255) NULL DEFAULT NULL,
  `usr_colonia` VARCHAR(255) NULL DEFAULT NULL,
  `usr_ciudad` VARCHAR(100) NULL DEFAULT NULL,
  `usr_estado` VARCHAR(100) NULL DEFAULT NULL,
  `usr_cp` VARCHAR(10) NULL DEFAULT NULL
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- Tabla `products`
-- Catálogo de productos.
-- --------------------------------------------------------
CREATE TABLE `products` (
  `prod_id` INT AUTO_INCREMENT PRIMARY KEY,
  `prod_name` VARCHAR(255) NOT NULL,
  `prod_imagen_url` VARCHAR(255) NOT NULL,
  `prod_spec_url` VARCHAR(255) NOT NULL,
  `prod_stock` INT UNSIGNED NOT NULL DEFAULT 0,
  `prod_price` DECIMAL(10, 2) NOT NULL
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- Tabla `order_status`
-- Estados posibles de un pedido (Ej. Completado, Pendiente).
-- --------------------------------------------------------
CREATE TABLE `order_status` (
  `os_id` INT AUTO_INCREMENT PRIMARY KEY,
  `os_name` VARCHAR(50) NOT NULL
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- Tabla `orders`
-- Almacena cada pedido.
-- Permite `usr_id` NULL para invitados.
-- Guarda una copia de los datos del cliente para cada pedido.
-- --------------------------------------------------------
CREATE TABLE `orders` (
  `ord_id` INT AUTO_INCREMENT PRIMARY KEY,
  `ord_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `os_id` INT NOT NULL,
  `usr_id` INT NULL DEFAULT NULL, -- Permite NULL para compras de invitados
  `ord_customer_name` VARCHAR(255) NOT NULL,
  `ord_customer_email` VARCHAR(255) NOT NULL,
  `ord_customer_phone` VARCHAR(20) NOT NULL,
  `ord_shipping_address` TEXT NOT NULL,
  FOREIGN KEY (`os_id`) REFERENCES `order_status`(`os_id`),
  FOREIGN KEY (`usr_id`) REFERENCES `users`(`usr_id`)
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- Tabla `order_details`
-- Conecta los productos específicos con cada pedido.
-- --------------------------------------------------------
CREATE TABLE `order_details` (
  `od_id` INT AUTO_INCREMENT PRIMARY KEY,
  `od_amount` INT NOT NULL,
  `prod_id` INT NOT NULL,
  `ord_id` INT NOT NULL,
  FOREIGN KEY (`prod_id`) REFERENCES `products`(`prod_id`),
  FOREIGN KEY (`ord_id`) REFERENCES `orders`(`ord_id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ===================================================================
-- INSERCIÓN DE DATOS INICIALES
-- ===================================================================

-- Insertar los estados de pedido básicos
INSERT INTO `order_status` (`os_id`, `os_name`) VALUES
(1, 'Completado'),
(2, 'Pendiente'),
(3, 'Cancelado'),
(4, 'Enviado');

-- Insertar productos de ejemplo
INSERT INTO `products` (`prod_id`, `prod_name`, `prod_imagen_url`, `prod_spec_url`, `prod_stock`, `prod_price`) VALUES
(1, 'Monitor Gamer Gigabyte GS25F2', 'img/art1.webp', 'img/specs/art1Desc.webp', 10, 2199.00),
(2, 'Procesador Intel Core i5-14400', 'img/art2.webp', 'img/specs/art2Desc.webp', 10, 3499.00),
(3, 'Procesador Intel Core i9-14900F', 'img/art3.webp', 'img/specs/art3Desc.webp', 10, 10999.00),
(4, 'Monitor Gamer MSI MAG 274F', 'img/art4.webp', 'img/specs/art4Desc.webp', 10, 2599.00),
(5, 'Tarjeta de Video XFX RX 9060 XT SWIFT PRO GAMING', 'img/art5.webp', 'img/specs/art5Desc.webp', 10, 6599.00),
(6, 'Tarjeta Madre AORUS X870E AORUS PRO ICE', 'img/art6.webp', 'img/specs/art6Desc.webp', 10, 6599.00),
(7, 'Procesador AMD Ryzen 5 5600X', 'img/art7.webp', 'img/specs/art7Desc.webp', 10, 2599.00),
(8, 'Monitor Curvo Profesional 32 NZXT CANVAS 32Q', 'img/art8.webp', 'img/specs/art8Desc.webp', 10, 4599.00),
(9, 'Procesador Intel Core Ultra 5 245KF', 'img/art9.webp', 'img/specs/art9Desc.webp', 10, 4599.00),
(10, 'Tarjeta de Video GIGABYTE NVIDIA GeForce RTX 5070 Ti', 'img/art10.webp', 'img/specs/art10Desc.webp', 10, 20999.99),
(11, 'Procesador AMD RYZEN 5 8600G', 'img/art11.webp', 'img/specs/art11Desc.webp', 10, 3499.00),
(12, 'Computadora GAMING MOOSE V2', 'img/art12.webp', 'img/specs/art12Desc.webp', 10, 15999.00),
(13, 'Tarjeta de Video GIGABYTE NVIDIA GeForce RTX 5070 WINDFORCE', 'img/art13.webp', 'img/specs/art13Desc.webp', 10, 11799.00),
(14, 'Procesador AMD Ryzen 5 9600X', 'img/art14.webp', 'img/specs/art14Desc.webp', 10, 4299.00),
(15, 'Procesador AMD Ryzen 7 7700X', 'img/art15.webp', 'img/specs/art15Desc.webp', 10, 5499.00),
(16, 'Laptop Gamer MSI Katana 15 HX', 'img/art16.webp', 'img/specs/art16Desc.webp', 10, 30999.00),
(17, 'Kit Memoria RAM G.Skill Flare X5', 'img/art17.webp', 'img/specs/art17Desc.webp', 10, 2399.00);