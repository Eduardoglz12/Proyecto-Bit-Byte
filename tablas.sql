-- --------------------------------------------------------
-- Tabla `users`
-- Almacena la información de los usuarios registrados y sus datos de envío.
-- --------------------------------------------------------
CREATE TABLE `users` (
  `usr_id` INT AUTO_INCREMENT PRIMARY KEY,
  `usr_user` VARCHAR(50) NOT NULL UNIQUE,
  `usr_password` VARCHAR(255) NOT NULL, -- VARCHAR(255) para contraseñas hasheadas
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
-- Almacena el catálogo de todos los productos disponibles en la tienda.
-- --------------------------------------------------------
CREATE TABLE `products` (
  `prod_id` INT AUTO_INCREMENT PRIMARY KEY,
  `prod_name` VARCHAR(255) NOT NULL,
  `prod_imagen_url` VARCHAR(255) NOT NULL,
  `prod_spec_url` VARCHAR(255) NOT NULL,
  `prod_stock` INT UNSIGNED NOT NULL DEFAULT 0,
  `prod_price` DECIMAL(10, 2) NOT NULL -- DECIMAL es el tipo ideal para dinero
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- Tabla `order_status`
-- Tabla de consulta para los diferentes estados de un pedido.
-- --------------------------------------------------------
CREATE TABLE `order_status` (
  `os_id` INT AUTO_INCREMENT PRIMARY KEY,
  `os_name` VARCHAR(50) NOT NULL
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- Tabla `orders`
-- Almacena la información de cada compra realizada.
-- `usr_id` puede ser NULL para permitir compras de invitados.
-- --------------------------------------------------------
CREATE TABLE `orders` (
  `ord_id` INT AUTO_INCREMENT PRIMARY KEY,
  `ord_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `os_id` INT NOT NULL,
  `usr_id` INT NULL DEFAULT NULL, -- Permite valores NULL para compras sin registro
  FOREIGN KEY (`os_id`) REFERENCES `order_status`(`os_id`),
  FOREIGN KEY (`usr_id`) REFERENCES `users`(`usr_id`)
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- Tabla `order_details`
-- Tabla de unión que conecta los productos con los pedidos.
-- --------------------------------------------------------
CREATE TABLE `order_details` (
  `od_id` INT AUTO_INCREMENT PRIMARY KEY,
  `od_amount` INT NOT NULL,
  `prod_id` INT NOT NULL,
  `ord_id` INT NOT NULL,
  FOREIGN KEY (`prod_id`) REFERENCES `products`(`prod_id`),
  FOREIGN KEY (`ord_id`) REFERENCES `orders`(`ord_id`) ON DELETE CASCADE -- Si se borra una orden, se borran sus detalles.
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

-- Insertar algunos productos de ejemplo para que la tienda no esté vacía
INSERT INTO `products` (`prod_name`, `prod_imagen_url`, `prod_spec_url`, `prod_stock`, `prod_price`) VALUES
('Tarjeta Gráfica RTX 4070 Super', 'img/art1.webp', 'img/specs/art1_specs.webp', 15, 12500.00),
('Procesador AMD Ryzen 7 7800X3D', 'img/art2.webp', 'img/specs/art2_specs.webp', 25, 7899.50),
('Monitor Gamer Curvo 27" 165Hz', 'img/art3.webp', 'img/specs/art3_specs.webp', 30, 4999.00),
('Kit de Memoria RAM DDR5 32GB (2x16GB)', 'img/art4.webp', 'img/specs/art4_specs.webp', 50, 2199.00),
('SSD NVMe 2TB Gen4', 'img/art5.webp', 'img/specs/art5_specs.webp', 40, 2850.75);

ALTER TABLE `orders`
ADD COLUMN `ord_customer_name` VARCHAR(255) NOT NULL AFTER `usr_id`,
ADD COLUMN `ord_customer_email` VARCHAR(255) NOT NULL AFTER `ord_customer_name`,
ADD COLUMN `ord_shipping_address` TEXT NOT NULL AFTER `ord_customer_email`;
ADD COLUMN `ord_customer_phone` VARCHAR(20) NOT NULL AFTER `ord_customer_email`;