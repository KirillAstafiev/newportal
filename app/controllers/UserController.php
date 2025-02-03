<?php
require_once 'app/models/User.php';

class UserController {
    private $model;

    public function __construct() {
        $this->model = new User(); // Создаём модель для работы с пользователями
    }

    // Показываем всех пользователей
    public function index() {
        $users = $this->model->getAllUsers(); // Получаем всех пользователей
        require 'app/views/users.php'; // Передаём в представление
    }

    // Показываем форму для добавления пользователя
    public function add() {
        require 'app/views/addUser.php'; // Форма добавления
    }

    // Добавляем нового пользователя
    public function store() {
        // Проверяем, пришли ли данные
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $this->model->addUser($name, $email); // Добавляем пользователя в базу
            header('Location: /user/index'); // Перенаправляем обратно на список пользователей
        }
    }

    // Удаляем пользователя
    public function delete() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $this->model->deleteUser($id); // Удаляем пользователя по ID
            header('Location: /user/index'); // Перенаправляем обратно на список пользователей
        }
    }
}
