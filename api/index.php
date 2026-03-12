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

class User {
    public $id;
    public $name;
    public $email;

    public function __construct(int $id, string $name, string $email) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
    }

    public function toJson(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email
        ];
    }
}

class UserDatabase {
    private $host = "localhost";
    private $user = "root";
    private $password = "";
    private $database = "database";

    private $conn;

    public function __construct() {
        try {
            $this->conn = new PDO("mysql:host=localhost;dbname=nome_do_banco", "root", "");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch(PDOException $e) {
            die("Erro: " . $e->getMessage());
        }
    }

    public function all() {
        $result = $this->conn->query("SELECT id, name, email FROM clientes");
        $users = [];

        if ($result->rowCount() > 0) {
            while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $users[] = new User($row['id'], $row['name'], $row['email']);
            }
        }

        return $users;
    }

    public function find(int $id) {
        $stmt = $this->conn->prepare("SELECT id, name, email FROM clientes WHERE id = ?");
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return new User($result['id'], $result['name'], $result['email']);
        }

        return null;
    }
}

function get_response(int $code, array $body): void {
    http_response_code($code);
    echo json_encode($body);
    exit;
}

$real_db = new UserDatabase();

if ($method !== 'GET') {
    get_response(405, ['status' => 'error', 'message' => 'Metodo nao permitido']);
}

// GET /api/clientes
if (preg_match('#^/clientes$#', $path)) {
    get_response(200, [
        'status' => 'success',
        'message' => 'Lista de clientes',
        'data' => array_map(fn($user) => $user->toJson(), $real_db->all())
    ]);
}

// GET /api/clientes/{id}
if (preg_match('#^/clientes/(\d+)$#', $path, $m)) {
    $cliente = $real_db->find((int)$m[1]);

    if (!$cliente) {
        get_response(404, ['status' => 'error', 'message' => 'Cliente nao encontrado']);
    }

    get_response(200, [
        'status' => 'success',
        'message' => 'Detalhes do cliente',
        'data' => $cliente->toJson()
    ]);
}

get_response(404, ['status' => 'error', 'message' => 'Rota nao encontrada']);