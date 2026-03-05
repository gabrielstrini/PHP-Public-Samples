<?php

header(header: 'Content-Type: application/json');

echo json_encode(value: [
"status" => `success`,
"message" => "Bem vindo a API",
"data" => 
    [
        "clients" => [
            [
                "name"=> "João", 
                "email"=> "joao@example.com",
                "phone"=> "123456789"],
            [
                "name"=> "Maria",
                "email"=> "maria@example.com",
                "phone"=> "987654321"
            ]
        ]
    ]
]);

?>