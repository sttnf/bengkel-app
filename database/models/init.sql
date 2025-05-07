-- Create Database
CREATE DATABASE IF NOT EXISTS db_service_system
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE db_service_system;

-- Users
CREATE TABLE users
(
    id            INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100)                                          NOT NULL,
    email         VARCHAR(100)                                          NOT NULL UNIQUE,
    password      VARCHAR(255)                                          NOT NULL,
    role          ENUM ('admin', 'customer', 'manager', 'receptionist') NOT NULL DEFAULT 'customer',
    phone_number  VARCHAR(20),
    is_active     BOOLEAN                                                        DEFAULT TRUE,
    profile_image VARCHAR(255),
    created_at    TIMESTAMP                                                      DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP                                                      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (email),
    INDEX (role)
) ENGINE = InnoDB;

-- Technicians
CREATE TABLE technicians
(
    id               INT AUTO_INCREMENT PRIMARY KEY,
    name             VARCHAR(100) NOT NULL,
    specialization   VARCHAR(100) NOT NULL,
    secondary_skills JSON,
    phone_number     VARCHAR(20)  NOT NULL,
    email            VARCHAR(100) UNIQUE,
    experience_years INT       DEFAULT 0,
    is_active        BOOLEAN   DEFAULT TRUE,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (specialization)
) ENGINE = InnoDB;

-- Service Categories
CREATE TABLE service_categories
(
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB;

-- Services
CREATE TABLE services
(
    id              INT AUTO_INCREMENT PRIMARY KEY,
    category_id     INT            NOT NULL,
    name            VARCHAR(100)   NOT NULL UNIQUE,
    description     TEXT,
    base_price      DECIMAL(10, 2) NOT NULL,
    estimated_hours DECIMAL(4, 2)  NOT NULL,
    is_active       BOOLEAN   DEFAULT TRUE,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES service_categories (id) ON DELETE RESTRICT,
    INDEX (category_id),
    INDEX (is_active)
) ENGINE = InnoDB;

-- Vehicle Brands
CREATE TABLE vehicle_brands
(
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB;

-- Vehicle Models
CREATE TABLE vehicle_models
(
    id         INT AUTO_INCREMENT PRIMARY KEY,
    brand_id   INT          NOT NULL,
    name       VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (brand_id) REFERENCES vehicle_brands (id) ON DELETE CASCADE,
    UNIQUE (brand_id, name)
) ENGINE = InnoDB;

-- Customer Vehicles
CREATE TABLE customer_vehicles
(
    id            INT AUTO_INCREMENT PRIMARY KEY,
    user_id       INT         NOT NULL,
    model_id      INT         NOT NULL,
    year          INT         NOT NULL,
    license_plate VARCHAR(20) NOT NULL UNIQUE,
    color         VARCHAR(50),
    vin_number    VARCHAR(50) UNIQUE,
    notes         TEXT,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    FOREIGN KEY (model_id) REFERENCES vehicle_models (id) ON DELETE RESTRICT,
    INDEX (user_id),
    INDEX (license_plate)
) ENGINE = InnoDB;

-- Service Requests
CREATE TABLE service_requests
(
    id              INT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT                                                                NOT NULL,
    vehicle_id      INT                                                                NOT NULL,
    service_id      INT                                                                NOT NULL,
    technician_id   INT,
    scheduled_date  DATE                                                               NOT NULL,
    scheduled_time  TIME                                                               NOT NULL,
    customer_notes  TEXT,
    diagnosis       TEXT,
    status          ENUM ('pending','confirmed','in_progress','completed','cancelled') NOT NULL DEFAULT 'pending',
    actual_price    DECIMAL(10, 2),
    completion_date DATETIME,
    created_at      TIMESTAMP                                                                   DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP                                                                   DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id),
    FOREIGN KEY (vehicle_id) REFERENCES customer_vehicles (id),
    FOREIGN KEY (service_id) REFERENCES services (id),
    FOREIGN KEY (technician_id) REFERENCES technicians (id) ON DELETE SET NULL,
    INDEX (status),
    INDEX (scheduled_date)
) ENGINE = InnoDB;

-- Inventory Items
CREATE TABLE inventory_items
(
    id            INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100)   NOT NULL,
    category      VARCHAR(50)    NOT NULL,
    description   TEXT,
    part_number   VARCHAR(50) UNIQUE,
    current_stock INT            NOT NULL DEFAULT 0,
    unit_price    DECIMAL(10, 2) NOT NULL,
    reorder_level INT            NOT NULL DEFAULT 5,
    supplier      VARCHAR(100),
    location      VARCHAR(50),
    is_active     BOOLEAN                 DEFAULT TRUE,
    created_at    TIMESTAMP               DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP               DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (current_stock),
    INDEX (category)
) ENGINE = InnoDB;

-- Parts Used in Service
CREATE TABLE service_parts
(
    id         INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT            NOT NULL,
    item_id    INT            NOT NULL,
    quantity   INT            NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    subtotal   DECIMAL(10, 2) GENERATED ALWAYS AS (quantity * unit_price) STORED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES service_requests (id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES inventory_items (id),
    INDEX (request_id)
) ENGINE = InnoDB;

-- Payments
CREATE TABLE payments
(
    id             INT AUTO_INCREMENT PRIMARY KEY,
    request_id     INT                                                                      NOT NULL,
    amount         DECIMAL(10, 2)                                                           NOT NULL,
    payment_method ENUM ('cash', 'credit_card', 'debit_card', 'transfer', 'digital_wallet') NOT NULL,
    transaction_id VARCHAR(100),
    payment_date   TIMESTAMP                                           DEFAULT CURRENT_TIMESTAMP,
    status         ENUM ('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    notes          TEXT,
    FOREIGN KEY (request_id) REFERENCES service_requests (id),
    INDEX (status)
) ENGINE = InnoDB;

-- Service Reviews
CREATE TABLE service_reviews
(
    id          INT AUTO_INCREMENT PRIMARY KEY,
    request_id  INT     NOT NULL UNIQUE,
    rating      TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    review_text TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES service_requests (id) ON DELETE CASCADE,
    INDEX (rating)
) ENGINE = InnoDB;

-- Users (pengguna)
INSERT INTO users (name, email, password, role, phone_number)
VALUES ('Andi Wijaya', 'andi.wijaya@gmail.com', 'hashed_password', 'customer', '081234567890'),
       ('Budi Santoso', 'budi.santoso@gmail.com', 'hashed_password', 'customer', '082233445566'),
       ('Clara Sari', 'clara.sari@gmail.com', 'hashed_password', 'receptionist', '081298765432'),
       ('Dian Prasetyo', 'dian.prasetyo@gmail.com', 'hashed_password', 'admin', '081122334455');

-- Technicians (teknisi)
INSERT INTO technicians (name, specialization, secondary_skills, phone_number, email, experience_years)
VALUES ('Rudi Hartono', 'Mesin', '[
  "Rem",
  "Suspensi"
]', '081300011122', 'rudi.hartono@bengkel.com', 5),
       ('Agus Salim', 'Kelistrikan', '[
         "AC",
         "Audio"
       ]', '081311122233', 'agus.salim@bengkel.com', 3);

-- Service Categories
INSERT INTO service_categories (name, description)
VALUES ('Mesin', 'Servis terkait mesin mobil seperti tune-up, ganti oli, dan overhaul'),
       ('Transmisi', 'Servis transmisi manual dan otomatis'),
       ('Kelistrikan', 'Servis sistem kelistrikan dan kelistrikan AC'),
       ('Rem', 'Pemeriksaan dan servis sistem pengereman'),
       ('AC', 'Servis sistem pendingin udara kendaraan'),
       ('Suspensi', 'Servis sistem suspensi dan kemudi'),
       ('Perawatan Berkala', 'Servis rutin seperti ganti oli, filter, dan pengecekan umum');

-- Services (layanan)
INSERT INTO services (category_id, name, description, base_price, estimated_hours)
VALUES (1, 'Ganti Oli Mesin', 'Penggantian oli mesin standar', 250000, 1.0),
       (1, 'Tune Up Mesin', 'Penyetelan ulang mesin agar performa optimal', 350000, 1.5),
       (3, 'Servis Kelistrikan', 'Pengecekan kabel dan kelistrikan utama', 300000, 1.2),
       (5, 'Servis AC', 'Isi freon dan pengecekan kompresor AC', 400000, 2.0),
       (4, 'Ganti Kampas Rem', 'Penggantian kampas rem depan atau belakang', 275000, 1.3);

-- Vehicle Brands
INSERT INTO vehicle_brands (name)
VALUES ('Toyota'),
       ('Honda'),
       ('Mitsubishi'),
       ('Daihatsu'),
       ('Suzuki');

-- Vehicle Models
INSERT INTO vehicle_models (brand_id, name)
VALUES (1, 'Avanza'),
       (1, 'Innova'),
       (2, 'Civic'),
       (2, 'Brio'),
       (3, 'Xpander'),
       (4, 'Sigra'),
       (5, 'Ertiga');

-- Customer Vehicles
INSERT INTO customer_vehicles (user_id, model_id, year, license_plate, color, vin_number, notes)
VALUES (1, 1, 2020, 'B 1234 ABC', 'Hitam', 'VIN123456789001', 'Servis terakhir: 3 bulan lalu'),
       (2, 3, 2019, 'D 5678 DEF', 'Putih', 'VIN987654321002', 'Bunyi aneh di mesin'),
       (1, 4, 2022, 'B 2345 XYZ', 'Merah', 'VIN112233445566', 'Perlu servis berkala');

-- Inventory Items
INSERT INTO inventory_items (name, category, description, part_number, current_stock, unit_price, reorder_level,
                             supplier, location)
VALUES ('Oli Mesin 10W-40', 'Oli', 'Oli sintetik 10W-40', 'OLI1040-AX', 20, 85000, 5, 'Pertamina Lubricants', 'Rak A1'),
       ('Kampas Rem Depan', 'Rem', 'Kampas rem keramik universal', 'KPD-BRM001', 10, 150000, 3, 'Bengkel Jaya Motor',
        'Rak B2'),
       ('Filter Udara', 'Filter', 'Filter udara kabin', 'FUA-CBN002', 15, 50000, 4, 'Auto Filter Indonesia', 'Rak C1');
