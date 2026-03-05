<?php

# Este é um exemplo simples de API RESTful em PHP sem usar frameworks, apenas para ajudar no aprendizado.
# author: @gabrielstrini
# github: https://github.com/gabrielstrini
# repository: https://github.com/gabrielstrini/php-public-samples/tree/main/api

header('Content-Type: application/json');
# IMPORTANTE: Se vc criou uma pasta "a", tem que trocar o prefixo, no meu caso é /php-public-samples/api, se fosse "a" seria /a
$folder_name = '/php-public-samples/api';
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

// remove prefixo /api
$path = preg_replace('#^' . preg_quote($folder_name, '#') . '(?:/|$)#', '/', $path);
$path = rtrim($path, '/');
if ($path === '') {
    $path = '/';
}

# Para debug, descomente a linha abaixo
#echo "Method: $method, Path: $path\n";

class UserDatabase {
    private $users = [
        ['id' => 1, 'name' => 'João', 'email' => 'joao@example.com'],
        ['id' => 2, 'name' => 'Maria', 'email' => 'maria@example.com'],
    ];

    public function all() {
        return $this->users;
    }

    public function find(int $id) {
        foreach ($this->users as $user) {
            if ($user['id'] === $id) {
                return $user;
            }
        }
        return null;
    }
}

function get_response(int $code, array $body): void {
    http_response_code($code);
    echo json_encode($body);
    exit;
}

$fake_db = new UserDatabase();

if ($method !== 'GET') {
    get_response(405, ['status' => 'error', 'message' => 'Metodo nao permitido']);
}

// GET /api/clientes
if (preg_match('#^/clientes$#', $path)) {
    get_response(200, [
        'status' => 'success',
        'message' => 'Lista de clientes',
        'data' => $fake_db->all()
    ]);
}

// GET /api/clientes/{id}
if (preg_match('#^/clientes/(\d+)$#', $path, $m)) {
    $cliente = $fake_db->find((int)$m[1]);

    if (!$cliente) {
        get_response(404, ['status' => 'error', 'message' => 'Cliente nao encontrado']);
    }

    get_response(200, [
        'status' => 'success',
        'message' => 'Detalhes do cliente',
        'data' => $cliente
    ]);
}

get_response(404, ['status' => 'error', 'message' => 'Rota nao encontrada']);