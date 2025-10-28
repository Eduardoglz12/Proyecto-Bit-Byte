CREATE TABLE users(
	usr_id INT NOT NULL AUTO_INCREMENT,
    usr_user VARCHAR(64) NOT NULL,
    usr_password VARCHAR(256)
);

CREATE TABLE products(
	prod_id INT NOT NULL AUTO_INCREMENT,
    prod_name VARCHAR(128) NOT NULL,
    prod_stock INT NOT DEFAULT 0,
    prod_price DECIMAL(7,2) NOT NULL
);

CREATE TABLE order_status(
	os_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    os_name VARCHAR(32)
);

CREATE TABLE orders(
	ord_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    ord_date DATE DEFAULT DATETIME,
    os_id INT NOT NULL,
    usr_id INT NOT NULL,
    
    FOREIGN KEY (os_id) REFERENCES order_status(os_id)
    	ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (usr_id) REFERENCES users(usr_id)
    	ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE order_details(
	od_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    od_amount INT NOT NULL,
    prod_id INT NOT NULL,
    ord_id INT NOT NULL,
    
    FOREIGN KEY (prod_id) REFERENCES products(prod_id)
    	ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (ord_id) REFERENCES orders(ord_id)
    	ON DELETE CASCADE ON UPDATE CASCADE
);