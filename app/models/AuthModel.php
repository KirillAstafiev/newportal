<?php

class AuthModel {
    private $ldap_server = "192.168.5.3";
    private $ldap_base_dn = "DC=auto,DC=local";

    public function authenticate($username, $password) {
        $ldap_user = "AUTO\\" . $username;

        $ldap_conn = ldap_connect($this->ldap_server);
        if (!$ldap_conn) {
            return ["error" => "Ошибка подключения к LDAP-серверу."];
        }

        ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);

        if (@ldap_bind($ldap_conn, $ldap_user, $password)) {
            $search_filter = "(sAMAccountName={$username})";
            $attributes = ["givenName", "sn", "mail"];
            $search_result = ldap_search($ldap_conn, $this->ldap_base_dn, $search_filter, $attributes);

            if ($search_result) {
                $entries = ldap_get_entries($ldap_conn, $search_result);

                if ($entries["count"] > 0) {
                    $user = $entries[0];

                    ldap_unbind($ldap_conn);

                    return [
                        "status" => "success",
                        "username" => $username,
                        "first_name" => $user["givenname"][0] ?? "Не указано",
                        "last_name" => $user["sn"][0] ?? "Не указано",
                        "email" => $user["mail"][0] ?? "Не указано"
                    ];
                } else {
                    return ["error" => "Пользователь не найден в LDAP."];
                }
            } else {
                return ["error" => "Ошибка выполнения поиска в LDAP."];
            }
        } else {
            return ["error" => "Ошибка авторизации. Проверьте логин и пароль."];
        }
    }
}
