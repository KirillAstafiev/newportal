<?php

require_once "app/models/AuthModel.php";

class AuthController {
    public function login() {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        header('Content-Type: application/json');

        // Обработка preflight-запроса (OPTIONS)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header("Access-Control-Max-Age: 86400");
            http_response_code(204);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Получаем входные данные
            $input = json_decode(file_get_contents('php://input'), true);

            if (!isset($input['username']) || !isset($input['password'])) {
                echo json_encode(["error" => "Пожалуйста, передайте логин и пароль."]);
                exit;
            }

            $username = trim($input['username']);
            $password = trim($input['password']);

            if (empty($username) || empty($password)) {
                echo json_encode(["error" => "Логин и пароль не могут быть пустыми."]);
                exit;
            }

            // Вызываем модель для авторизации
            $authModel = new AuthModel();
            $response = $authModel->authenticate($username, $password);

            echo json_encode($response);
        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
            echo json_encode(["message" => "Этот GET-запрос успешно обработан."]);
        } else {
            echo json_encode(["error" => "Используйте метод POST или GET."]);
        }
    }
}
