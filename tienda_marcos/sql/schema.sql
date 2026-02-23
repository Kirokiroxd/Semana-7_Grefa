CREATE DATABASE IF NOT EXISTS tienda_random CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE tienda_random;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(190) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(190) NOT NULL,
  price_cents INT NOT NULL,
  stock INT NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  subtotal_cents INT NOT NULL,
  tax_cents INT NOT NULL,
  total_cents INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  name VARCHAR(190) NOT NULL,
  unit_price_cents INT NOT NULL,
  qty INT NOT NULL,
  line_total_cents INT NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id),
  FOREIGN KEY (product_id) REFERENCES products(id)
);

INSERT INTO products (name, price_cents, stock) VALUES
('Audífonos X', 1599, 20),
('Mouse Gamer', 1299, 15),
('Teclado Mecánico', 4999, 8),
('Cargador USB-C', 899, 30),
('Termo', 1099, 25);