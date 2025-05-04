-- Create the cafe database
CREATE DATABASE IF NOT EXISTS cafe_db;
USE cafe_db;

-- Create categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create menu items table
CREATE TABLE IF NOT EXISTS menu_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255),
    is_featured BOOLEAN DEFAULT FALSE,
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'staff') DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create customers table
CREATE TABLE IF NOT EXISTS customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT,
    order_number VARCHAR(20) UNIQUE NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    total_amount DECIMAL(10, 2) NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    special_instructions TEXT,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
);

-- Create order items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    menu_item_id INT,
    quantity INT NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE SET NULL
);

-- Create contact messages table
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_read BOOLEAN DEFAULT FALSE
);

-- Insert default admin user
INSERT INTO users (username, password, email, full_name, role) 
VALUES ('admin', '$2y$10$8FPnT0I.Qh4i6yO3skV1N.pZCE/a5JMW0C6cN/WnEQ0c41SzT6sYu', 'admin@cafewebsite.com', 'Admin User', 'admin');
-- Password is 'admin123' hashed with bcrypt

-- Insert some default categories
INSERT INTO categories (name, description) VALUES
('Hot Beverages', 'Warm drinks to brighten your day'),
('Cold Beverages', 'Refreshing cold drinks'),
('Pastries', 'Freshly baked pastries'),
('Sandwiches', 'Delicious sandwiches'),
('Desserts', 'Sweet treats to finish your meal');

-- Insert some sample menu items
INSERT INTO menu_items (category_id, name, description, price, is_featured) VALUES
(1, 'Cappuccino', 'Espresso with steamed milk and foam', 4.50, TRUE),
(1, 'Latte', 'Espresso with steamed milk', 4.00, FALSE),
(1, 'Americano', 'Espresso with hot water', 3.50, FALSE),
(2, 'Iced Coffee', 'Cold brewed coffee over ice', 4.25, TRUE),
(2, 'Lemonade', 'Fresh homemade lemonade', 3.75, FALSE),
(3, 'Croissant', 'Buttery, flaky pastry', 3.25, TRUE),
(3, 'Blueberry Muffin', 'Moist muffin filled with blueberries', 3.50, FALSE),
(4, 'Turkey Club', 'Turkey, bacon, lettuce, and tomato on toasted bread', 8.95, TRUE),
(4, 'Veggie Wrap', 'Seasonal vegetables with hummus in a wrap', 7.95, FALSE),
(5, 'Chocolate Cake', 'Rich chocolate layer cake', 5.95, TRUE),
(5, 'Cheesecake', 'Creamy New York style cheesecake', 5.50, FALSE); 