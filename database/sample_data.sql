-- Sample Data for NEXTGEN eCommerce Analytics
USE nextgen;

-- Clear existing sample data (if any)
DELETE FROM order_items WHERE order_id IN (SELECT id FROM orders WHERE id > 10);
DELETE FROM orders WHERE id > 10;
DELETE FROM reviews WHERE id > 5;

-- Insert sample products with realistic prices and stock
INSERT INTO products (name, brand, category_id, description, price, image, stock) VALUES 
('iPhone 15 Pro Max', 'Apple', 1, 'Latest iPhone with titanium design and advanced camera', 399999.00, 'assets/images/products/iphone15-pro-max.jpg', 15),
('Samsung Galaxy S24 Ultra', 'Samsung', 1, 'Premium Android with S Pen and AI features', 349999.00, 'assets/images/products/galaxy-s24-ultra.jpg', 12),
('Google Pixel 8 Pro', 'Google', 1, 'Best camera phone with AI capabilities', 299999.00, 'assets/images/products/pixel-8-pro.jpg', 8),
('OnePlus 12', 'OnePlus', 1, 'Fast performance with Hasselblad camera', 199999.00, 'assets/images/products/oneplus-12.jpg', 20),
('iPad Pro 12.9"', 'Apple', 2, 'Professional tablet with M2 chip', 249999.00, 'assets/images/products/ipad-pro.jpg', 10),
('Samsung Galaxy Tab S9', 'Samsung', 2, 'Premium Android tablet experience', 189999.00, 'assets/images/products/galaxy-tab-s9.jpg', 15),
('AirPods Pro 2nd Gen', 'Apple', 3, 'Active noise cancellation earbuds', 49999.00, 'assets/images/products/airpods-pro.jpg', 30),
('Samsung Galaxy Watch 6', 'Samsung', 4, 'Advanced health monitoring smartwatch', 89999.00, 'assets/images/products/galaxy-watch-6.jpg', 25),
('Apple Watch Series 9', 'Apple', 4, 'Premium smartwatch with health features', 129999.00, 'assets/images/products/apple-watch-9.jpg', 18),
('Sony WH-1000XM5', 'Sony', 3, 'Premium noise-cancelling headphones', 79999.00, 'assets/images/products/sony-headphones.jpg', 22)
ON DUPLICATE KEY UPDATE 
    price = VALUES(price),
    stock = VALUES(stock);

-- Insert sample customers
INSERT INTO users (name, email, password, phone, address, role) VALUES 
('John Silva', 'john.silva@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+94771234567', '123 Main St, Colombo 03', 'user'),
('Maria Fernando', 'maria.fernando@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+94772345678', '456 Lake Rd, Kandy', 'user'),
('David Perera', 'david.perera@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+94773456789', '789 Hill St, Galle', 'user'),
('Sarah Rodrigo', 'sarah.rodrigo@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+94774567890', '321 Beach Rd, Negombo', 'user'),
('Michael Dias', 'michael.dias@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+94775678901', '654 Temple Rd, Anuradhapura', 'user'),
('Lisa Mendis', 'lisa.mendis@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+94776789012', '987 Market St, Jaffna', 'user'),
('Robert Jayasuriya', 'robert.jayasuriya@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+94777890123', '147 Garden Rd, Nuwara Eliya', 'user'),
('Amanda Wickramasinghe', 'amanda.wickramasinghe@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+94778901234', '258 Forest Rd, Ratnapura', 'user'),
('James Bandara', 'james.bandara@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+94779012345', '369 River Rd, Kurunegala', 'user'),
('Emma Gunasekara', 'emma.gunasekara@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+94770123456', '741 Mountain Rd, Badulla', 'user')
ON DUPLICATE KEY UPDATE 
    phone = VALUES(phone),
    address = VALUES(address);

-- Insert realistic sample orders with varying dates (last 90 days)
INSERT INTO orders (user_id, total_amount, status, shipping_address, billing_address, payment_method, created_at) VALUES 
-- Recent orders (last 7 days)
(2, 449998.00, 'delivered', '456 Lake Rd, Kandy', '456 Lake Rd, Kandy', 'card', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(3, 299999.00, 'delivered', '789 Hill St, Galle', '789 Hill St, Galle', 'paypal', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(4, 79999.00, 'shipped', '321 Beach Rd, Negombo', '321 Beach Rd, Negombo', 'card', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(5, 189999.00, 'processing', '654 Temple Rd, Anuradhapura', '654 Temple Rd, Anuradhapura', 'bank_transfer', NOW()),
(6, 129999.00, 'pending', '987 Market St, Jaffna', '987 Market St, Jaffna', 'card', NOW()),

-- Orders from last week
(7, 399999.00, 'delivered', '147 Garden Rd, Nuwara Eliya', '147 Garden Rd, Nuwara Eliya', 'card', DATE_SUB(NOW(), INTERVAL 8 DAY)),
(8, 49999.00, 'delivered', '258 Forest Rd, Ratnapura', '258 Forest Rd, Ratnapura', 'paypal', DATE_SUB(NOW(), INTERVAL 9 DAY)),
(9, 249999.00, 'delivered', '369 River Rd, Kurunegala', '369 River Rd, Kurunegala', 'card', DATE_SUB(NOW(), INTERVAL 10 DAY)),
(10, 89999.00, 'delivered', '741 Mountain Rd, Badulla', '741 Mountain Rd, Badulla', 'card', DATE_SUB(NOW(), INTERVAL 11 DAY)),
(2, 199999.00, 'delivered', '456 Lake Rd, Kandy', '456 Lake Rd, Kandy', 'paypal', DATE_SUB(NOW(), INTERVAL 12 DAY)),

-- Orders from last month
(3, 349999.00, 'delivered', '789 Hill St, Galle', '789 Hill St, Galle', 'card', DATE_SUB(NOW(), INTERVAL 15 DAY)),
(4, 79999.00, 'delivered', '321 Beach Rd, Negombo', '321 Beach Rd, Negombo', 'card', DATE_SUB(NOW(), INTERVAL 18 DAY)),
(5, 129999.00, 'delivered', '654 Temple Rd, Anuradhapura', '654 Temple Rd, Anuradhapura', 'bank_transfer', DATE_SUB(NOW(), INTERVAL 22 DAY)),
(6, 399999.00, 'delivered', '987 Market St, Jaffna', '987 Market St, Jaffna', 'card', DATE_SUB(NOW(), INTERVAL 25 DAY)),
(7, 49999.00, 'delivered', '147 Garden Rd, Nuwara Eliya', '147 Garden Rd, Nuwara Eliya', 'paypal', DATE_SUB(NOW(), INTERVAL 28 DAY)),

-- Orders from 2 months ago
(8, 249999.00, 'delivered', '258 Forest Rd, Ratnapura', '258 Forest Rd, Ratnapura', 'card', DATE_SUB(NOW(), INTERVAL 35 DAY)),
(9, 189999.00, 'delivered', '369 River Rd, Kurunegala', '369 River Rd, Kurunegala', 'card', DATE_SUB(NOW(), INTERVAL 42 DAY)),
(10, 89999.00, 'delivered', '741 Mountain Rd, Badulla', '741 Mountain Rd, Badulla', 'paypal', DATE_SUB(NOW(), INTERVAL 49 DAY)),
(2, 299999.00, 'delivered', '456 Lake Rd, Kandy', '456 Lake Rd, Kandy', 'card', DATE_SUB(NOW(), INTERVAL 56 DAY)),
(3, 79999.00, 'delivered', '789 Hill St, Galle', '789 Hill St, Galle', 'card', DATE_SUB(NOW(), INTERVAL 63 DAY)),

-- Orders from 3 months ago
(4, 399999.00, 'delivered', '321 Beach Rd, Negombo', '321 Beach Rd, Negombo', 'card', DATE_SUB(NOW(), INTERVAL 70 DAY)),
(5, 129999.00, 'delivered', '654 Temple Rd, Anuradhapura', '654 Temple Rd, Anuradhapura', 'bank_transfer', DATE_SUB(NOW(), INTERVAL 77 DAY)),
(6, 49999.00, 'delivered', '987 Market St, Jaffna', '987 Market St, Jaffna', 'paypal', DATE_SUB(NOW(), INTERVAL 84 DAY)),
(7, 249999.00, 'delivered', '147 Garden Rd, Nuwara Eliya', '147 Garden Rd, Nuwara Eliya', 'card', DATE_SUB(NOW(), INTERVAL 91 DAY));

-- Insert order items for the orders above
INSERT INTO order_items (order_id, product_id, quantity, price) VALUES 
-- Order 1: iPhone 15 Pro Max + AirPods Pro
(1, 1, 1, 399999.00),
(1, 7, 1, 49999.00),

-- Order 2: Google Pixel 8 Pro
(2, 3, 1, 299999.00),

-- Order 3: Sony Headphones
(3, 10, 1, 79999.00),

-- Order 4: Samsung Galaxy Tab S9
(4, 6, 1, 189999.00),

-- Order 5: Apple Watch Series 9
(5, 9, 1, 129999.00),

-- Order 6: iPhone 15 Pro Max
(6, 1, 1, 399999.00),

-- Order 7: AirPods Pro
(7, 7, 1, 49999.00),

-- Order 8: iPad Pro
(8, 5, 1, 249999.00),

-- Order 9: Samsung Galaxy Watch 6
(9, 8, 1, 89999.00),

-- Order 10: OnePlus 12
(10, 4, 1, 199999.00),

-- Order 11: Samsung Galaxy S24 Ultra
(11, 2, 1, 349999.00),

-- Order 12: Sony Headphones
(12, 10, 1, 79999.00),

-- Order 13: Apple Watch Series 9
(13, 9, 1, 129999.00),

-- Order 14: iPhone 15 Pro Max
(14, 1, 1, 399999.00),

-- Order 15: AirPods Pro
(15, 7, 1, 49999.00),

-- Order 16: iPad Pro
(16, 5, 1, 249999.00),

-- Order 17: Samsung Galaxy Tab S9
(17, 6, 1, 189999.00),

-- Order 18: Samsung Galaxy Watch 6
(18, 8, 1, 89999.00),

-- Order 19: Google Pixel 8 Pro
(19, 3, 1, 299999.00),

-- Order 20: Sony Headphones
(20, 10, 1, 79999.00),

-- Order 21: iPhone 15 Pro Max
(21, 1, 1, 399999.00),

-- Order 22: Apple Watch Series 9
(22, 9, 1, 129999.00),

-- Order 23: AirPods Pro
(23, 7, 1, 49999.00),

-- Order 24: iPad Pro
(24, 5, 1, 249999.00);

-- Insert sample reviews
INSERT INTO reviews (product_id, user_id, rating, comment, created_at) VALUES 
(1, 2, 5, 'Amazing phone! The camera quality is outstanding and battery life is excellent.', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(2, 3, 4, 'Great Android phone with excellent performance. S Pen is very useful.', DATE_SUB(NOW(), INTERVAL 8 DAY)),
(3, 4, 5, 'Best camera phone I\'ve ever used. AI features are incredible.', DATE_SUB(NOW(), INTERVAL 12 DAY)),
(7, 5, 4, 'Great sound quality and noise cancellation works perfectly.', DATE_SUB(NOW(), INTERVAL 15 DAY)),
(9, 6, 5, 'Excellent health tracking features and smooth performance.', DATE_SUB(NOW(), INTERVAL 20 DAY));

-- Insert sample user activity logs
INSERT INTO user_activity_logs (user_id, action, details, ip_address, created_at) VALUES 
(2, 'login', 'User logged in successfully', '192.168.1.100', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(2, 'purchase', 'Order #1 completed - iPhone 15 Pro Max + AirPods Pro', '192.168.1.100', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(3, 'login', 'User logged in successfully', '192.168.1.101', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(3, 'purchase', 'Order #2 completed - Google Pixel 8 Pro', '192.168.1.101', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(4, 'login', 'User logged in successfully', '192.168.1.102', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(4, 'purchase', 'Order #3 completed - Sony Headphones', '192.168.1.102', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(5, 'login', 'User logged in successfully', '192.168.1.103', NOW()),
(5, 'purchase', 'Order #4 completed - Samsung Galaxy Tab S9', '192.168.1.103', NOW()),
(6, 'login', 'User logged in successfully', '192.168.1.104', NOW()),
(6, 'purchase', 'Order #5 completed - Apple Watch Series 9', '192.168.1.104', NOW());

-- Update order statuses to be more realistic
UPDATE orders SET 
    status = CASE 
        WHEN created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY) THEN 'pending'
        WHEN created_at >= DATE_SUB(NOW(), INTERVAL 3 DAY) THEN 'processing'
        WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 'shipped'
        ELSE 'delivered'
    END
WHERE id > 0; 