CREATE DATABASE IF NOT EXISTS lumiere_books
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE lumiere_books;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS addresses;
DROP TABLE IF EXISTS favorites;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS cart_items;
DROP TABLE IF EXISTS contact_messages;
DROP TABLE IF EXISTS books;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS settings;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    phone VARCHAR(30) NULL,
    birth_date DATE NULL,
    gender ENUM('female', 'male', 'other') NULL,
    avatar VARCHAR(255) NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Akun demo untuk praktik USK.
-- Password disimpan dalam bentuk hash dan tidak ditampilkan pada halaman website.
INSERT INTO users (name, email, phone, password_hash, role, status) VALUES
(
    'Admin Lumière Books',
    'admin@lumierebooks.local',
    '081200000001',
    '$2y$12$SxD200O1adIHII7VZHxb0ubw3JcsVDjuvDzKjhcUoPD/mtIf.ftXy',
    'admin',
    'active'
),
(
    'Pelanggan Demo Lumière Books',
    'user@lumierebooks.local',
    '081200000002',
    '$2y$12$SdZzHg7Zjnf7g.gwsypb2uWS8MKcit2/m7Ery4VfGBqKenaWQb7au',
    'user',
    'active'
);

CREATE TABLE addresses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    label VARCHAR(60) NOT NULL DEFAULT 'Rumah',
    recipient_name VARCHAR(120) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    province VARCHAR(100) NOT NULL,
    city VARCHAR(100) NOT NULL,
    district VARCHAR(100) NULL,
    village VARCHAR(100) NULL,
    postal_code VARCHAR(15) NOT NULL,
    address_detail TEXT NOT NULL,
    is_default TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_addresses_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_addresses_user (user_id)
) ENGINE=InnoDB;

CREATE TABLE notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(180) NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(40) NOT NULL DEFAULT 'system',
    target_url VARCHAR(255) NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_notifications_user_read (user_id, is_read)
) ENGINE=InnoDB;

CREATE TABLE categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(120) NOT NULL UNIQUE,
    description TEXT NULL,
    image VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE books (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id BIGINT UNSIGNED NULL,
    sku VARCHAR(40) NOT NULL UNIQUE,
    title VARCHAR(180) NOT NULL,
    author VARCHAR(140) NOT NULL,
    publisher VARCHAR(140) NULL,
    isbn VARCHAR(40) NULL,
    description TEXT NULL,
    price DECIMAL(12,2) NOT NULL DEFAULT 0,
    stock INT UNSIGNED NOT NULL DEFAULT 0,
    cover VARCHAR(255) NULL,
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_books_category FOREIGN KEY (category_id)
        REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_books_title (title),
    INDEX idx_books_author (author),
    INDEX idx_books_status (status)
) ENGINE=InnoDB;

CREATE TABLE favorites (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_favorite_user_book (user_id, book_id),
    CONSTRAINT fk_favorites_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_favorites_book FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE cart_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    quantity INT UNSIGNED NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_cart_user_book (user_id, book_id),
    CONSTRAINT fk_cart_user FOREIGN KEY (user_id)
        REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_cart_book FOREIGN KEY (book_id)
        REFERENCES books(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_code VARCHAR(40) NOT NULL UNIQUE,
    user_id BIGINT UNSIGNED NOT NULL,
    recipient_name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    shipping_address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    province VARCHAR(100) NOT NULL,
    postal_code VARCHAR(15) NOT NULL,
    courier VARCHAR(80) NULL,
    tracking_number VARCHAR(100) NULL,
    shipping_cost DECIMAL(12,2) NOT NULL DEFAULT 0,
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    total DECIMAL(12,2) NOT NULL DEFAULT 0,
    payment_method ENUM('bank_transfer', 'qris', 'cod') NOT NULL,
    payment_status ENUM('pending', 'unpaid', 'paid', 'failed', 'expired', 'refunded') NOT NULL DEFAULT 'pending',
    order_status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending',
    stock_released TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id)
        REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_orders_status (order_status),
    INDEX idx_orders_payment (payment_status),
    INDEX idx_orders_created (created_at)
) ENGINE=InnoDB;

CREATE TABLE order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NULL,
    title VARCHAR(180) NOT NULL,
    author VARCHAR(140) NOT NULL,
    price DECIMAL(12,2) NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    CONSTRAINT fk_order_items_order FOREIGN KEY (order_id)
        REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_order_items_book FOREIGN KEY (book_id)
        REFERENCES books(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL UNIQUE,
    provider VARCHAR(40) NOT NULL DEFAULT 'demo',
    transaction_id VARCHAR(100) NULL,
    payment_type VARCHAR(60) NULL,
    amount DECIMAL(12,2) NOT NULL,
    status ENUM('pending', 'paid', 'failed', 'expired', 'refunded') NOT NULL DEFAULT 'pending',
    response_json LONGTEXT NULL,
    paid_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_payments_order FOREIGN KEY (order_id)
        REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE contact_messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL,
    subject VARCHAR(180) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied') NOT NULL DEFAULT 'new',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO categories (name, slug, description, image) VALUES
('Fiksi', 'fiksi', 'Novel, cerita, dan karya sastra pilihan.', 'assets/categories/fiksi.jpg'),
('Non-Fiksi', 'non-fiksi', 'Buku pengetahuan, biografi, dan referensi.', 'assets/categories/non-fiksi.jpg'),
('Self-Help', 'self-help', 'Pengembangan diri dan kebiasaan positif.', 'assets/categories/self-help.jpg'),
('Bisnis & Ekonomi', 'bisnis-ekonomi', 'Bisnis, keuangan, dan ekonomi.', 'assets/categories/bisnis-ekonomi.jpg'),
('Pendidikan', 'pendidikan', 'Buku pembelajaran dan pendidikan.', 'assets/categories/pendidikan.jpg'),
('Anak-Anak', 'anak-anak', 'Bacaan edukatif dan cerita anak.', 'assets/categories/anak-anak.jpg'),
('Remaja', 'remaja', 'Bacaan untuk remaja.', 'assets/categories/remaja.jpg'),
('Horor', 'horor', 'Cerita misteri dan horor.', 'assets/categories/horor.jpg'),
('Romance', 'romance', 'Novel romantis.', 'assets/categories/romance.jpg'),
('Klasik', 'klasik', 'Karya sastra klasik.', 'assets/categories/klasik.jpg'),
('Sejarah', 'sejarah', 'Buku sejarah dan budaya.', 'assets/categories/klasik.jpg'),
('Thriller', 'thriller', 'Cerita penuh misteri dan ketegangan.', 'assets/categories/horor.jpg');

INSERT INTO books
(category_id, sku, title, author, publisher, isbn, description, price, stock, cover, is_featured)
VALUES
(
    1,
    'LUM-0001',
    'The Midnight Library',
    'Matt Haig',
    'Canongate Books',
    '9781786892737',
    'Di antara hidup dan mati terdapat sebuah perpustakaan yang menyimpan pilihan-pilihan hidup yang mungkin dijalani.',
    95000,
    25,
    'assets/covers/midnight.png',
    1
),
(
    3,
    'LUM-0002',
    'Atomic Habits',
    'James Clear',
    'Gramedia Pustaka Utama',
    '9786020633176',
    'Panduan praktis membangun kebiasaan baik dan menghilangkan kebiasaan buruk melalui perubahan kecil.',
    89000,
    32,
    'assets/covers/atomic.png',
    1
),
(
    9,
    'LUM-0003',
    'Dilan: Dia adalah Dilanku Tahun 1990',
    'Pidi Baiq',
    'Pastel Books',
    '9786027870992',
    'Kisah cinta remaja dengan latar Bandung pada tahun 1990.',
    75000,
    18,
    'assets/covers/dilan.png',
    1
),
(
    1,
    'LUM-0004',
    'Bumi',
    'Tere Liye',
    'Gramedia Pustaka Utama',
    '9786020332956',
    'Petualangan dunia paralel yang penuh rahasia dan persahabatan.',
    80000,
    27,
    'assets/covers/bumi.png',
    1
),
(
    8,
    'LUM-0005',
    'Danur',
    'Risa Saraswati',
    'Bukune',
    '9786022201800',
    'Kisah pengalaman dan persahabatan dari dunia yang tak kasatmata.',
    78000,
    14,
    'assets/covers/danur.png',
    1
),
(
    11,
    'LUM-0006',
    'Laut Bercerita',
    'Leila S. Chudori',
    'Kepustakaan Populer Gramedia',
    '9786024246945',
    'Novel tentang keluarga, persahabatan, kehilangan, dan sejarah.',
    85000,
    21,
    'assets/covers/laut.png',
    1
),
(
    12,
    'LUM-0007',
    'The Da Vinci Code',
    'Dan Brown',
    'Doubleday',
    '9780307474278',
    'Thriller misteri yang membawa pembaca menelusuri teka-teki sejarah dan simbol.',
    110000,
    11,
    'assets/covers/davinci.png',
    0
),
(
    3,
    'LUM-0008',
    'Ikigai',
    'Héctor García & Francesc Miralles',
    'Penguin Books',
    '9781786330895',
    'Menemukan alasan untuk bangun setiap pagi melalui filosofi hidup Jepang.',
    90000,
    20,
    'assets/covers/ikigai.png',
    1
);

INSERT INTO settings (setting_key, setting_value) VALUES
('store_name', 'Lumière Books'),
('store_email', 'hello@lumiere_books.com'),
('store_phone', '0812 0000 0000'),
('store_address', 'Indonesia'),
('store_description', 'Buku hari ini, dunia yang lebih luas esok.');
