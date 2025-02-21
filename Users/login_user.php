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

if ($_SERVER['REQUEST_METHOD'] === 'POST'){ 
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

    $ldap_server = "192.168.5.3";
    $ldap_base_dn = "DC=auto,DC=local";
    $ldap_user = "AUTO\\" . $username;

    $ldap_conn = ldap_connect($ldap_server);
    if (!$ldap_conn) {
        echo json_encode(["error" => "Ошибка подключения к LDAP-серверу."]);
        exit;
    }

    ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);

    if (@ldap_bind($ldap_conn, $ldap_user, $password)) {
        $search_filter = "(sAMAccountName={$username})";
        $attributes = ["givenName", "sn", "mail"];
        $search_result = ldap_search($ldap_conn, $ldap_base_dn, $search_filter, $attributes);

        if ($search_result) {
            $entries = ldap_get_entries($ldap_conn, $search_result);

            if ($entries["count"] > 0) {
                $user = $entries[0];

                $response = [
                    "user" => [
                        "username" => $username,
                        "first_name" => $user["givenname"][0] ?? "Не указано",
                        "last_name" => $user["sn"][0] ?? "Не указано",
                        "email" => $user["mail"][0] ?? "Не указано"
                    ]
                ];
            } else {
                $response = ["error" => "Пользователь не найден в LDAP."];
            }
        } else {
            $response = ["error" => "Ошибка выполнения поиска в LDAP."];
        }
    } else {
        $response = ["error" => "Ошибка авторизации. Проверьте логин и пароль."];
    }

    ldap_unbind($ldap_conn);
    echo json_encode($response);

} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $response = [
        "message" => "Этот GET-запрос успешно обработан."
    ];
    echo json_encode($response);
} else {
    echo json_encode(["error" => "Используйте метод POST или GET."]);
}