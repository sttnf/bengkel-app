-- Create database with proper character set and collation
CREATE
    DATABASE IF NOT EXISTS db_service_system
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE
    db_service_system;

-- User Management
CREATE TABLE users
(
    user_id       INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100)                                          NOT NULL,
    email         VARCHAR(100)                                          NOT NULL UNIQUE,
    password      VARCHAR(255)                                          NOT NULL,
    role          ENUM ('admin', 'customer', 'manager', 'receptionist') NOT NULL DEFAULT 'customer',
    phone_number  VARCHAR(20),
    is_active     BOOLEAN                                                        DEFAULT TRUE,
    profile_image VARCHAR(255),
    created_at    TIMESTAMP                                                      DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP                                                      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_email (email),
    INDEX idx_user_role (role)
) ENGINE = InnoDB;

-- Technician Management
CREATE TABLE technicians
(
    technician_id    INT AUTO_INCREMENT PRIMARY KEY,
    name             VARCHAR(100) NOT NULL,
    specialization   VARCHAR(100) NOT NULL COMMENT 'Primary skill area: Engine, Electrical, Body, etc.',
    secondary_skills JSON COMMENT 'Array of additional skills',
    phone_number     VARCHAR(20)  NOT NULL,
    email            VARCHAR(100) UNIQUE,
    experience_years INT       DEFAULT 0,
    is_active        BOOLEAN   DEFAULT TRUE,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_technician_specialization (specialization)
) ENGINE = InnoDB;

-- Service Categories
CREATE TABLE service_categories
(
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB;

-- Services Offered
CREATE TABLE services
(
    service_id      INT AUTO_INCREMENT PRIMARY KEY,
    category_id     INT            NOT NULL,
    name            VARCHAR(100)   NOT NULL,
    description     TEXT,
    base_price      DECIMAL(10, 2) NOT NULL,
    estimated_hours DECIMAL(4, 2)  NOT NULL COMMENT 'Estimated service duration in hours',
    is_active       BOOLEAN   DEFAULT TRUE,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES service_categories (category_id) ON DELETE RESTRICT,
    UNIQUE KEY (name),
    INDEX idx_service_category (category_id),
    INDEX idx_service_status (is_active)
) ENGINE = InnoDB;

-- Vehicle Brands
CREATE TABLE vehicle_brands
(
    brand_id   INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB;

-- Vehicle Models
CREATE TABLE vehicle_models
(
    model_id   INT AUTO_INCREMENT PRIMARY KEY,
    brand_id   INT          NOT NULL,
    name       VARCHAR(100) NOT NULL,
    FOREIGN KEY (brand_id) REFERENCES vehicle_brands (brand_id) ON DELETE CASCADE,
    UNIQUE KEY (brand_id, name),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB;

-- Customer Vehicles
CREATE TABLE customer_vehicles
(
    vehicle_id    INT AUTO_INCREMENT PRIMARY KEY,
    user_id       INT         NOT NULL,
    model_id      INT         NOT NULL,
    year          INT         NOT NULL,
    license_plate VARCHAR(20) NOT NULL UNIQUE,
    color         VARCHAR(50),
    vin_number    VARCHAR(50) UNIQUE,
    notes         TEXT,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE,
    FOREIGN KEY (model_id) REFERENCES vehicle_models (model_id) ON DELETE RESTRICT,
    INDEX idx_vehicle_user (user_id),
    INDEX idx_vehicle_plate (license_plate)
) ENGINE = InnoDB;

-- Service Requests
CREATE TABLE service_requests
(
    request_id      INT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT                                                                    NOT NULL,
    vehicle_id      INT                                                                    NOT NULL,
    service_id      INT                                                                    NOT NULL,
    technician_id   INT,
    scheduled_date  DATE                                                                   NOT NULL,
    scheduled_time  TIME                                                                   NOT NULL,
    customer_notes  TEXT,
    diagnosis       TEXT,
    status          ENUM ('pending', 'confirmed', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    actual_price    DECIMAL(10, 2),
    completion_date DATETIME,
    created_at      TIMESTAMP                                                                       DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP                                                                       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE RESTRICT,
    FOREIGN KEY (vehicle_id) REFERENCES customer_vehicles (vehicle_id) ON DELETE RESTRICT,
    FOREIGN KEY (service_id) REFERENCES services (service_id) ON DELETE RESTRICT,
    FOREIGN KEY (technician_id) REFERENCES technicians (technician_id) ON DELETE SET NULL,
    INDEX idx_service_status (status),
    INDEX idx_service_date (scheduled_date)
) ENGINE = InnoDB;

-- Parts Inventory
CREATE TABLE inventory_items
(
    item_id       INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100)   NOT NULL,
    category      VARCHAR(50)    NOT NULL,
    description   TEXT,
    part_number   VARCHAR(50) UNIQUE,
    current_stock INT            NOT NULL DEFAULT 0,
    unit_price    DECIMAL(10, 2) NOT NULL,
    reorder_level INT            NOT NULL DEFAULT 5,
    supplier      VARCHAR(100),
    location      VARCHAR(50) COMMENT 'Storage location in the shop',
    is_active     BOOLEAN                 DEFAULT TRUE,
    created_at    TIMESTAMP               DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP               DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_inventory_stock (current_stock),
    INDEX idx_inventory_category (category)
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
    FOREIGN KEY (request_id) REFERENCES service_requests (request_id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES inventory_items (item_id) ON DELETE RESTRICT,
    INDEX idx_part_service (request_id)
) ENGINE = InnoDB;

-- Service Payments
CREATE TABLE payments
(
    payment_id     INT AUTO_INCREMENT PRIMARY KEY,
    request_id     INT                                                                      NOT NULL,
    amount         DECIMAL(10, 2)                                                           NOT NULL,
    payment_method ENUM ('cash', 'credit_card', 'debit_card', 'transfer', 'digital_wallet') NOT NULL,
    transaction_id VARCHAR(100),
    payment_date   TIMESTAMP                                           DEFAULT CURRENT_TIMESTAMP,
    status         ENUM ('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    notes          TEXT,
    FOREIGN KEY (request_id) REFERENCES service_requests (request_id) ON DELETE RESTRICT,
    INDEX idx_payment_status (status)
) ENGINE = InnoDB;

-- Customer Reviews
CREATE TABLE service_reviews
(
    review_id   INT AUTO_INCREMENT PRIMARY KEY,
    request_id  INT     NOT NULL UNIQUE,
    rating      TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    review_text TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES service_requests (request_id) ON DELETE CASCADE,
    INDEX idx_review_rating (rating)
) ENGINE = InnoDB;

-- Insert initial service categories
INSERT INTO service_categories (name, description)
VALUES ('Engine', 'All engine-related services including tune-ups and repairs'),
       ('Transmission', 'Manual and automatic transmission services'),
       ('Electrical', 'Vehicle electrical system diagnostics and repairs'),
       ('Brakes', 'Brake system inspection, maintenance and repairs'),
       ('AC System', 'Air conditioning system services and repairs'),
       ('Suspension', 'Suspension and steering system services'),
       ('Routine Maintenance', 'Regular maintenance services like oil changes and inspections');

-- Insert common vehicle brands
INSERT INTO vehicle_brands (name)
VALUES ('Toyota'),
       ('Honda'),
       ('Suzuki'),
       ('Daihatsu'),
       ('Mitsubishi'),
       ('Nissan'),
       ('BMW'),
       ('Mercedes-Benz'),
       ('Hyundai'),
       ('Kia');

-- Create a view for service request summary
CREATE VIEW view_service_summary AS
SELECT sr.request_id,
       u.name                                       AS customer_name,
       CONCAT(vb.name, ' ', vm.name, ' (', cv.year, ')') AS vehicle,
       cv.license_plate,
       s.name                                            AS service_name,
       sr.scheduled_date,
       sr.scheduled_time,
       t.full_name                                       AS technician_name,
       sr.status,
       sr.actual_price,
       (SELECT SUM(sp.subtotal)
        FROM service_parts sp
        WHERE sp.request_id = sr.request_id)             AS parts_cost,
       (SELECT SUM(p.amount)
        FROM payments p
        WHERE p.request_id = sr.request_id
          AND p.status = 'completed')                    AS paid_amount
FROM service_requests sr
         JOIN users u ON sr.user_id = u.user_id
         JOIN customer_vehicles cv ON sr.vehicle_id = cv.vehicle_id
         JOIN vehicle_models vm ON cv.model_id = vm.model_id
         JOIN vehicle_brands vb ON vm.brand_id = vb.brand_id
         JOIN services s ON sr.service_id = s.service_id
         LEFT JOIN technicians t ON sr.technician_id = t.technician_id;