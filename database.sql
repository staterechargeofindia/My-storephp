CREATE DATABASE ecommerce_platform;
USE ecommerce_platform;

CREATE TABLE admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(100),
  password VARCHAR(255)
);

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(100) UNIQUE,
  phone VARCHAR(15),
  password VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE merchants (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  shop_name VARCHAR(150),
  email VARCHAR(100) UNIQUE,
  phone VARCHAR(15),
  password VARCHAR(255),
  kyc_status ENUM('pending','verified','rejected') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE merchant_kyc (
  id INT AUTO_INCREMENT PRIMARY KEY,
  merchant_id INT,
  aadhaar VARCHAR(20),
  pan VARCHAR(20),
  gst VARCHAR(20),
  msme VARCHAR(30),
  account_name VARCHAR(100),
  account_number VARCHAR(50),
  ifsc VARCHAR(20),
  address TEXT,
  pincode VARCHAR(10),
  district VARCHAR(50),
  state VARCHAR(50),
  photo VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (merchant_id) REFERENCES merchants(id)
);

CREATE TABLE admin_otp (
  id INT AUTO_INCREMENT PRIMARY KEY,
  merchant_id INT,
  otp VARCHAR(10),
  is_used ENUM('yes','no') DEFAULT 'no',
  expires_at DATETIME,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  status ENUM('active','inactive') DEFAULT 'active'
);

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  merchant_id INT,
  category_id INT,
  name VARCHAR(150),
  mrp DECIMAL(10,2),
  price DECIMAL(10,2),
  discount_percent INT,
  description TEXT,
  status ENUM('active','inactive') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE product_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT,
  image VARCHAR(255)
);

CREATE TABLE banners (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  image VARCHAR(255),
  link VARCHAR(255),
  status ENUM('active','inactive') DEFAULT 'active'
);

CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  merchant_id INT,
  total_amount DECIMAL(10,2),
  payment_method ENUM('COD','ONLINE'),
  status ENUM('placed','confirmed','dispatched','delivered','cancelled') DEFAULT 'placed',
  delivery_date DATE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT,
  product_id INT,
  quantity INT,
  price DECIMAL(10,2)
);

CREATE TABLE settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  upi_id VARCHAR(100),
  site_name VARCHAR(100)
);
