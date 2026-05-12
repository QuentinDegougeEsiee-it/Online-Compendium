CREATE DATABASE IF NOT EXISTS monster_hunter_db;
USE monster_hunter_db;

CREATE TABLE monsters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    species VARCHAR(50),
    description TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; -- UTF-8

CREATE TABLE items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    rarity INT,
    monster_id INT,
    FOREIGN KEY (monster_id) REFERENCES monsters(id) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;