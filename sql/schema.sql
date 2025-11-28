CREATE DATABASE IF NOT EXISTS online_computer_store;
USE online_computer_store;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255),
    category VARCHAR(100),
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_price DECIMAL(10,2),
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT 'Pending',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT,
    price DECIMAL(10,2),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE IF NOT EXISTS groups_h (
  id INT PRIMARY KEY,
  name VARCHAR(200) NOT NULL
);

CREATE TABLE IF NOT EXISTS subcategories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  group_id INT NOT NULL,
  name VARCHAR(255) NOT NULL,
  FOREIGN KEY (group_id) REFERENCES groups_h(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS types (
  id INT AUTO_INCREMENT PRIMARY KEY,
  subcategory_id INT NOT NULL,
  name VARCHAR(255) NOT NULL,
  FOREIGN KEY (subcategory_id) REFERENCES subcategories(id) ON DELETE CASCADE
);

ALTER TABLE products ADD COLUMN group_id INT NULL;
ALTER TABLE products ADD COLUMN subcategory_id INT NULL;
ALTER TABLE products ADD COLUMN type_id INT NULL;

ALTER TABLE products
  ADD CONSTRAINT fk_products_group
  FOREIGN KEY (group_id) REFERENCES groups_h(id) ON DELETE SET NULL;

ALTER TABLE products
  ADD CONSTRAINT fk_products_subcat
  FOREIGN KEY (subcategory_id) REFERENCES subcategories(id) ON DELETE SET NULL;

ALTER TABLE products
  ADD CONSTRAINT fk_products_type
  FOREIGN KEY (type_id) REFERENCES types(id) ON DELETE SET NULL;

INSERT INTO groups_h (id, name) VALUES
(1, 'Laptops'),
(2, 'Desktop Computers'),
(3, 'PC Components'),
(4, 'Displays & Monitors'),
(5, 'Peripherals & Accessories'),
(6, 'Networking Devices'),
(7, 'Power & Backup'),
(8, 'Storage Devices'),
(9, 'Cables & Adapters'),
(10, 'Smart Office Devices'),
(11, 'Software & Enterprise Solutions')
ON DUPLICATE KEY UPDATE name = VALUES(name);