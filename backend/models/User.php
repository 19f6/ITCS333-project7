<?php

class User {
    private $pdo;
    private $id;
    private $name;
    private $email;
    private $password;

    public function __construct($pdo, $id = null, $name = null, $email = null, $password = null) {
        $this->pdo = $pdo;
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }

    public function register() {
        // check if email already exists
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$this->email]);
        if ($stmt->rowCount() > 0) {
            throw new Exception("Email already registered");
        }

        // hash the password using default php hashing func.
        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$this->name, $this->email, $hashedPassword]);

        return $this->pdo->lastInsertId();
    }

    public function getUserByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function login($email, $password) {
        $stmt = $this->pdo->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }

        $this->id = $user['id'];
        $this->name = $user['name'];
        $this->email = $email;

        return true;
    }

    // getters
    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getEmail() {
        return $this->email;
    }
}
