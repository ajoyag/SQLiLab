CREATE DATABASE IF NOT EXISTS sqli_lab;
USE sqli_lab;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL,
  email VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL
);

INSERT INTO users (username, email, password) VALUES
('alice', 'alice@example.com', 'alicepwd'),
('bob', 'bob@example.com', 'bobpwd'),
('charlie', 'charlie@example.com', 'charliepwd');
