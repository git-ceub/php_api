CREATE DATABASE IF NOT EXISTS exemplo
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE exemplo;

CREATE TABLE IF NOT EXISTS tutor (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO tutor (nome, email) VALUES
    ('Maria Silva', 'maria.silva@exemplo.com'),
    ('João Santos', 'joao.santos@exemplo.com');
