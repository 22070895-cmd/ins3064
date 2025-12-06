
CREATE DATABASE ins3064 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ins3064;

-- 1. Bảng người dùng
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Bảng sản phẩm (có ảnh)
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    image_url VARCHAR(500) DEFAULT 'https://via.placeholder.com/400x400/cccccc/666666?text=No+Image',
    price DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    quantity INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. Bảng giỏ hàng (cart)
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_user_product (user_id, product_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. Bảng đơn hàng (orders) – chỉ lưu thông tin chung
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_code VARCHAR(50) UNIQUE NOT NULL,           -- VD: DH20251225123456
    user_id INT NOT NULL,
    total_amount DECIMAL(15,2) NOT NULL,
    status ENUM('pending','paid') DEFAULT 'pending',
    payment_method ENUM('qr','cod','bank') DEFAULT 'qr',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. Bảng chi tiết đơn hàng (order_items) – lưu từng sản phẩm trong đơn
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price_at_purchase DECIMAL(15,2) NOT NULL,
    
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;


INSERT INTO products (name, image_url, price, quantity) VALUES
('iPhone 17 Pro Max', 'https://www.findgsm.com/uploads/gadgets/appleiphone17promax-b8ebbb.jpg', 42990000, 30),
('iPhone 16 Pro Max', 'https://www.apple.com/newsroom/images/2024/09/apple-debuts-iphone-16-pro-and-iphone-16-pro-max/article/Apple-iPhone-16-Pro-hero-geo-240909_inline.jpg.large_2x.jpg', 34990000, 25),
('iPhone 15 Pro Max', 'https://m.media-amazon.com/images/I/81UKVHM77GL._AC_.jpg', 24990000, 15),
('iPhone 14 Pro Max', 'https://tse1.explicit.bing.net/th/id/OIP.muM3lyNF2vObnlAQZDhoKQHaJP?cb=ucfimg2&ucfimg=1&rs=1&pid=ImgDetMain&o=7&rm=3', 18990000, 20),
('iPhone 13 Pro Max', 'https://m-cdn.phonearena.com/images/reviews/245091-image/BK6A9342.jpg', 13990000, 10),
('iPhone 16', 'https://cdn.hoanghamobile.com/i/productlist/dsp/Uploads/2024/09/10/ip16-xanh-mong-ket.png', 23990000, 20),
('iPhone 15', 'https://tse4.mm.bing.net/th/id/OIP.M7P8ugaNJgy2bU8M-S0bUgHaHa?cb=ucfimg2&ucfimg=1&w=500&h=500&rs=1&pid=ImgDetMain&o=7&rm=3', 21990000, 20),
('iPhone 14', 'https://tse3.mm.bing.net/th/id/OIP.hniOlD8e5OcVq4WQyrsqQwHaHa?cb=ucfimg2&ucfimg=1&rs=1&pid=ImgDetMain&o=7&rm=3', 13990000, 25),
('MacBook Air M4', 'https://cdn.mos.cms.futurecdn.net/zBTcxtwtWhBVQjeNDpxSu8-840-80.jpg', 31417000, 8),
('MacBook Pro M5', 'https://9to5mac.com/wp-content/uploads/sites/6/2025/02/m5-macbook-pro.jpg?quality=82&strip=all&w=1600', 41990000, 12),
('AirPods 4', 'https://cdn.hoanghamobile.com/Uploads/2024/09/13/airpods-4-pdp-image-position-2-vn-vi_638618354917943069.jpg', 3590000, 30),
('AirPods Pro 3 2025', 'https://applemagazine.com/wp-content/uploads/2025/01/air-pods-pro-2-1536x1024.png', 6790000, 20),
('AirPods Pro 2', 'https://media.cnn.com/api/v1/images/stellar/prod/220921163441-airpods-pro-2-review-1.jpg?c=16x9', 5190000, 20),
('Apple Watch Series 11', 'https://www.apple.com/v/apple-watch-series-11/a/images/overview/product-viewer/product_landing_endframe__eaytrp6zz6c2_large.jpg', 10890000, 10),
('Apple Watch Series 10', 'https://tse4.mm.bing.net/th/id/OIP.e5xZhqO0mYZ8_7Ii7tiRpgHaHa?cb=ucfimg2&ucfimg=1&w=1306&h=1306&rs=1&pid=ImgDetMain&o=7&rm=3', 8490000, 30),
('Apple Watch Ultra 3', 'https://iphone-mania.jp/wp-content/uploads/2024/08/20/Apple-Watch-Ultra-3-concept_1200.jpg', 23490000, 15),
('Apple Watch Ultra 2', 'https://tse4.mm.bing.net/th/id/OIP.ILYrhXp5_1Yza5ZFPXliSwHaHa?cb=ucfimg2&ucfimg=1&rs=1&pid=ImgDetMain&o=7&rm=3', 19990000, 20),
('Apple Watch SE 3', 'https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/watch-compare-se-202509?wid=520&hei=520&fmt=jpeg&qlt=90&.v=eEpjZGlsbzI4YmtuR2pKQXNDTzZ5eThnZFRkdzMwY2NsY2I5Y3NQL214QzM2dk9rVWpEampSQXBqK3dUclB1WEdjSkVFV1FxeHRkZDFvRXAwaDZkVGRCVnRkbnoxcUU4aG9vT2t1SVBnd28', 5990000, 50);


ALTER TABLE users 
ADD COLUMN email VARCHAR(255) NULL AFTER username,
ADD UNIQUE KEY unique_email (email);