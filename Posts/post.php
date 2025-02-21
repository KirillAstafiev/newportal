<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    exit;
}

header('Content-Type: application/json');

$serverName = "sql1";
$connectionOptions = [
    "Database" => "portal",
    "Uid" => "sa",
    "PWD" => "P@ssw0rd",
    "CharacterSet" => "UTF-8"
];

$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn === false) {
    echo json_encode([
        "status" => "failed",
        "message" => "Ошибка подключения к базе данных: " . print_r(sqlsrv_errors(), true)
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT * FROM posts";
    $stmt = sqlsrv_query($conn, $sql);

    if ($stmt === false) {
        echo json_encode([
            "status" => "failed",
            "message" => "Ошибка выполнения запроса: " . print_r(sqlsrv_errors(), true)
        ]);
        exit;
    }

    $response = [
        "status" => "failed",
        "posts" => []
    ];

    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $response["posts"][] = $row;
    }

    if (!empty($response["posts"])) {
        $response["status"] = "success";
    } else {
        $response["message"] = "Нет данных";
    }

    echo json_encode($response);
} else {
    echo json_encode([
        "status" => "failed",
        "message" => "Используйте метод GET"
    ]);
}

sqlsrv_close($conn);