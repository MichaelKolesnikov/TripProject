CREATE TABLE IF NOT EXISTS `Role` (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL UNIQUE
);

INSERT INTO `Role` (id, name) VALUES
(1, 'user'),
(2, 'manager');

CREATE TABLE IF NOT EXISTS `Hotel` (
    id INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    country VARCHAR(255) NOT NULL,
    city VARCHAR(255) NOT NULL,
    street VARCHAR(255) NOT NULL
);
INSERT INTO `Hotel` (id, name, country, city, street) VALUES (1, 'Главный отель', 'Россия', 'Москва', 'Москворечье');

CREATE TABLE IF NOT EXISTS `User` (
    id INT PRIMARY KEY AUTO_INCREMENT,
    `login` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    phone VARCHAR(255) NOT NULL,
    role_id INT NOT NULL DEFAULT 1,
    FOREIGN KEY (role_id) REFERENCES `Role`(id)
);

CREATE TABLE IF NOT EXISTS `Trip` (
    id INT PRIMARY KEY,
    `start_date` DATE NOT NULL DEFAULT CURRENT_DATE,
    end_date DATE NOT NULL DEFAULT CURRENT_DATE,
    `description` TEXT DEFAULT 'Описание',
    need_visa BOOLEAN DEFAULT FALSE,
    need_transfer BOOLEAN DEFAULT FALSE,
    need_culture_program BOOLEAN DEFAULT FALSE,
    cancelled BOOLEAN NOT NULL DEFAULT FALSE,
    cost DECIMAL(10, 2) NOT NULL DEFAULT 0,
    hotel_id INT DEFAULT 1,
    FOREIGN KEY (id) REFERENCES `User`(id),
    FOREIGN KEY (hotel_id) REFERENCES `Hotel`(id)
);

CREATE TABLE IF NOT EXISTS `Change` (
    id INT PRIMARY KEY,
    trip_id INT NOT NULL,
    manager_id INT,
    `description` TEXT,
    done BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (manager_id) REFERENCES `User`(id),
    FOREIGN KEY (trip_id) REFERENCES `Trip`(id)
);
