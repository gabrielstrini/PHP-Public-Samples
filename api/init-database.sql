-- Criar o banco de dados
CREATE DATABASE IF NOT EXISTS nome_do_banco;
USE nome_do_banco;

-- Criar tabela de clientes
CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE
);

-- Inserir dados de teste
INSERT INTO clientes (name, email) VALUES
('Gabriel Strini', 'gabrielstrini@gmail.com'),
('Pessoa 1', 'pessoa1@example.com'),
('Pessoa 2', 'pessoa2@example.com'),
('Pessoa 3', 'pessoa3@example.com');