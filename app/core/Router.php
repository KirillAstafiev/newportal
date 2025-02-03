<?php
class Router {
    public function handleRequest() {
        // Получаем URL
        $url = $_GET['url'] ?? 'auth/login';

        // Разбиваем URL
        $urlParts = explode('/', $url);
        $controllerName = ucfirst($urlParts[0]) . 'Controller';
        $methodName = $urlParts[1] ?? 'index';

        // Подключаем нужный контроллер
        require_once "app/controllers/$controllerName.php";

        // Создаём объект контроллера
        $controller = new $controllerName();

        // Вызываем метод
        $controller->$methodName();
    }
}

