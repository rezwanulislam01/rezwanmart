-- ============================================
-- RezwanMart Database
-- E-Commerce Project | CSE 3209 & 3210
-- Varendra University | Spring 2026
-- ============================================

CREATE DATABASE IF NOT EXISTS rezwanmart;
USE rezwanmart;

-- ============================================
-- Table: users
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- Table: categories
-- ============================================
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- Table: products
-- ============================================
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- ============================================
-- Table: orders
-- ============================================
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    shipping_address TEXT NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================
-- Table: order_items
-- ============================================
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- ============================================
-- Table: cart
-- ============================================
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- ============================================
-- Default Admin Account
-- Password: admin123
-- ============================================
INSERT INTO users (full_name, email, password, role)
VALUES ('Admin', 'admin@rezwanmart.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- ============================================
-- Sample Categories
-- ============================================
INSERT INTO categories (name) VALUES
('Electronics'),
('Clothing'),
('Books'),
('Home & Kitchen'),
('Sports');

-- ============================================
-- Sample Products
-- ============================================
INSERT INTO products (category_id, name, description, price, stock, image) VALUES
(1, 'Wireless Earbuds', 'High quality wireless earbuds with noise cancellation.', 1299.00, 50, 'default.jpg'),
(1, 'USB-C Hub', '7-in-1 USB-C hub with HDMI, USB 3.0 ports.', 899.00, 30, 'default.jpg'),
(2, 'Cotton T-Shirt', 'Premium cotton t-shirt, available in multiple colors.', 349.00, 100, 'default.jpg'),
(3, 'Web Development Book', 'Complete guide to modern web development.', 599.00, 25, 'default.jpg'),
(4, 'Coffee Maker', 'Automatic drip coffee maker with timer.', 2499.00, 15, 'default.jpg'),
(5, 'Yoga Mat', 'Non-slip yoga mat with carrying strap.', 799.00, 40, 'default.jpg');
