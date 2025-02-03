<?php
require_once 'app/core/Database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance(); // Подключаем базу данных
    }

    // Получаем всех пользователей
    public function getAllUsers() {
        $stmt = $this->db->query("SELECT * FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Добавляем нового пользователя
    public function addUser($name, $email) {
        $stmt = $this->db->prepare("INSERT INTO users (name, email) VALUES (:name, :email)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
    }

    // Удаляем пользователя по ID
    public function deleteUser($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }
}
