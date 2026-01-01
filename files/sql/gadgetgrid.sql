-- GadgetGrid Database Schema
-- Import this file into XAMPP phpMyAdmin

DROP DATABASE IF EXISTS gadgetgrid;
CREATE DATABASE gadgetgrid;
USE gadgetgrid;

-- Users Table (Admin, Employee, Customer)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    role ENUM('admin', 'employee', 'customer') NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'approved',
    profile_image VARCHAR(255) DEFAULT 'default.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories Table
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Products Table
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category_id INT,
    specifications JSON,
    image VARCHAR(255) DEFAULT 'product_default.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Inventory/Stock Table
CREATE TABLE inventory (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    reorder_level INT DEFAULT 10,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Stock Logs Table
CREATE TABLE stock_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    employee_id INT NOT NULL,
    action ENUM('stock_in', 'stock_out') NOT NULL,
    quantity INT NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Offers Table
CREATE TABLE offers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    discount_percentage DECIMAL(5, 2) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Orders Table
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order Items Table
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Wishlist Table
CREATE TABLE wishlist (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_wishlist (customer_id, product_id)
);

-- Insert Default Admin
INSERT INTO users (username, email, password, full_name, role, status) VALUES
('admin', 'admin@gadgetgrid.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/. og/at2.uheWG/igi', 'System Admin', 'admin', 'approved');
-- Default password: password

-- Insert Sample Categories
INSERT INTO categories (name, description) VALUES
('Mobiles', 'Smartphones and mobile devices'),
('Watches', 'Smart watches and fitness trackers'),
('Earbuds', 'Wireless earbuds and headphones'),
('VR Headsets', 'Virtual reality headsets and accessories'),
('Tablets', 'Tablets and e-readers');

-- Insert Sample Products
INSERT INTO products (name, description, price, category_id, specifications) VALUES
('iPhone 15 Pro', 'Latest Apple smartphone with A17 chip', 999.99, 1, '{"storage": "256GB", "color": "Titanium", "display": "6.1 inch"}'),
('Samsung Galaxy S24', 'Flagship Android smartphone', 899.99, 1, '{"storage": "128GB", "color": "Black", "display": "6.2 inch"}'),
('Apple Watch Series 9', 'Advanced health and fitness tracker', 399.99, 2, '{"size": "45mm", "color": "Silver", "gps": true}'),
('Samsung Galaxy Watch 6', 'Premium smartwatch with rotating bezel', 349.99, 2, '{"size":  "44mm", "color":  "Black", "gps": true}'),
('AirPods Pro 2', 'Active noise cancellation earbuds', 249.99, 3, '{"noise_cancellation": true, "battery": "6 hours"}'),
('Sony WF-1000XM5', 'Premium wireless earbuds', 299.99, 3, '{"noise_cancellation": true, "battery": "8 hours"}'),
('Meta Quest 3', 'Mixed reality VR headset', 499.99, 4, '{"storage": "128GB", "resolution": "4K+"}'),
('iPad Pro 12.9', 'Professional tablet with M2 chip', 1099.99, 5, '{"storage": "256GB", "display": "12.9 inch Liquid Retina"}');

-- Insert Inventory for Products
INSERT INTO inventory (product_id, quantity, reorder_level) VALUES
(1, 50, 10),
(2, 45, 10),
(3, 30, 5),
(4, 25, 5),
(5, 100, 20),
(6, 80, 15),
(7, 20, 5),
(8, 35, 8);

-- Insert Sample Employee (pending approval)
INSERT INTO users (username, email, password, full_name, phone, role, status) VALUES
('employee1', 'employee1@gadgetgrid.com', '$2y$10$92IXUNpkjO0rOQ5byMi. Ye4oKoEa3Ro9llC/. og/at2.uheWG/igi', 'John Employee', '1234567890', 'employee', 'pending');

-- Insert Sample Customer
INSERT INTO users (username, email, password, full_name, phone, address, role, status) VALUES
('customer1', 'customer1@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane Customer', '0987654321', '123 Main Street, City', 'customer', 'approved');