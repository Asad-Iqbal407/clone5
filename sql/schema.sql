-- sql/schema.sql
CREATE DATABASE IF NOT EXISTS t4mshop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE t4mshop;

-- Users
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(100) NOT NULL,
  last_name  VARCHAR(100) NOT NULL,
  email      VARCHAR(191) NOT NULL UNIQUE,
  phone      VARCHAR(30),
  nif        VARCHAR(30),
  password_hash VARCHAR(255) NOT NULL,
  is_admin TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Categories (parent/child)
CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  icon VARCHAR(60) NULL,            -- Font Awesome class (e.g., fa-mobile-screen)
  parent_id INT NULL,
  CONSTRAINT fk_cat_parent FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Products (optional/simple demo)
CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT,
  name VARCHAR(150) NOT NULL,
  description TEXT,
  price DECIMAL(10,2) NOT NULL DEFAULT 0,
  image VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_prod_cat FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Repair requests
CREATE TABLE IF NOT EXISTS repair_requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  device VARCHAR(100) NOT NULL,
  issue VARCHAR(100) NOT NULL,
  description TEXT NOT NULL,
  contact_info VARCHAR(50) NOT NULL,
  urgency VARCHAR(20) DEFAULT 'normal',
  picture1 VARCHAR(255),
  picture2 VARCHAR(255),
  picture3 VARCHAR(255),
  status ENUM('new','in_review','ready','closed') DEFAULT 'new',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_rep_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Seed categories (top-level)
INSERT INTO categories (name, icon, parent_id) VALUES
('Smartphones/Cell Phones', 'fa-mobile-screen', NULL),
('Sound & Audio',           'fa-volume-high',  NULL),
('Gadgets',                 'fa-watch-smart',  NULL),
('SIM Cards',               'fa-sim-card',     NULL),
('Accessories',             'fa-rectangle-ad', NULL),
('Components',              'fa-microchip',    NULL),
('Tools',                   'fa-screwdriver-wrench', NULL),
('Computing',               'fa-display',      NULL);

-- Subcategories (a few from your screenshot)
-- Smartphones/Cell Phones (id will be 1 if inserted above in order)
INSERT INTO categories (name, parent_id) VALUES
('Smartphones', 1), ('Cell Phones', 1), ('Refurbished', 1), ('Landline', 1);

-- Sound & Audio
INSERT INTO categories (name, parent_id) VALUES
('Earbuds', 2), ('Microphones', 2), ('Speakers', 2), ('Audio Cables', 2), ('Neckband', 2), ('Earphones', 2);

-- Gadgets
INSERT INTO categories (name, parent_id) VALUES
('Accessories', 3), ('Digital Pen', 3), ('Smartwatches', 3), ('Surveillance Camera', 3), ('Toys', 3), ('Cigarette Lighter', 3);

-- SIM Cards
INSERT INTO categories (name, parent_id) VALUES ('Prepaid', 4), ('Postpaid', 4);

-- Accessories
INSERT INTO categories (name, parent_id) VALUES
('Cases', 5), ('OTG Adapter', 5), ('Ringlight', 5), ('Supports', 5), ('Adapters', 5), ('Glasses', 5),
('Cables', 5), ('Power Bank', 5), ('Battery', 5), ('Car Accessories', 5);

-- Components
INSERT INTO categories (name, parent_id) VALUES
('Lens', 6), ('Camera Lens', 6), ('LCD Connector', 6), ('Touch+Display', 6), ('Touch', 6),
('Display', 6), ('Flex', 6), ('Button', 6), ('Batteries', 6), ('Front/Back Camera', 6);

-- Tools
INSERT INTO categories (name, parent_id) VALUES
('Cleaning Tools', 7), ('Equipments', 7), ('Glues', 7), ('Microscopes and Magnifiers', 7),
('Opening Tools', 7), ('Pliers', 7), ('Screw', 7), ('Solder Wires', 7), ('Testing Tools', 7), ('Tweezers', 7);

-- Computing
INSERT INTO categories (name, parent_id) VALUES
('Adapters', 8), ('Bag for Laptop', 8), ('Display For Laptop', 8), ('Headphone', 8), ('Keyboard', 8),
('Mouse', 8), ('Mouse Pads', 8), ('Tablets', 8), ('Router', 8), ('Computer Speakers', 8);

-- Shopping cart sessions
CREATE TABLE IF NOT EXISTS cart_sessions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  session_id VARCHAR(255) NOT NULL UNIQUE,
  user_id INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_cart_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Cart items
CREATE TABLE IF NOT EXISTS cart_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cart_session_id INT NOT NULL,
  product_name VARCHAR(255) NOT NULL,
  product_price DECIMAL(10,2) NOT NULL,
  product_image VARCHAR(500),
  quantity INT NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_cart_item_session FOREIGN KEY (cart_session_id) REFERENCES cart_sessions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Insert admin user (predetermined)
-- Note: Run this after the ALTER TABLE statement above
-- INSERT INTO users (first_name, last_name, email, password_hash, is_admin) VALUES
-- ('Admin', 'User', 'admin@imobile.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1); -- password: password

-- Insert admin user (predetermined)
INSERT INTO users (first_name, last_name, email, password_hash, is_admin) VALUES
('Admin', 'User', 'admin@imobile.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1); -- password: password

-- A couple demo products (optional)
INSERT INTO products (category_id, name, description, price, image) VALUES
(1, 'Sample Smartphone', 'Demo smartphone item', 599.00, 'assets/img/phone.png'),
(2, 'Wireless Earbuds', 'Demo earbuds item', 49.99, 'assets/img/earbuds.png');