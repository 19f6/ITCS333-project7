<?php

    class CampusNews {
        private $pdo;

        // News properties
        private $id;
        private $title;
        private $body;
        private $tags;
        private $category;
        private $image;
        private $user_id;
        private $created_at;

        public function __construct($pdo) {
            $this->pdo = $pdo;
        }

        public function getAll() {
            $stmt = $this->pdo->prepare("SELECT * FROM campus_news ORDER BY created_at DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getById($id) {
            $stmt = $this->pdo->prepare("SELECT * FROM campus_news WHERE id = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function create($data, $userId) {
            $stmt = $this->pdo->prepare("
                INSERT INTO campus_news (title, body, tags, category, image, user_id)
                VALUES (:title, :body, :tags, :category, :image, :user_id)
            ");
            return $stmt->execute([
                ':title'    => $data['title'] ?? '',
                ':body'     => $data['body'] ?? '',
                ':tags'     => $data['tags'] ?? '',
                ':category' => $data['category'] ?? '',
                ':image'    => $data['image'] ?? '',
                ':user_id'  => $userId
            ]);
        }

        public function update($data, $userId) {
            $stmt = $this->pdo->prepare("
                UPDATE campus_news
                SET title = :title, body = :body, tags = :tags, category = :category, image = :image
                WHERE id = :id AND user_id = :user_id
            ");
            return $stmt->execute([
                ':title'    => $data['title'] ?? '',
                ':body'     => $data['body'] ?? '',
                ':tags'     => $data['tags'] ?? '',
                ':category' => $data['category'] ?? '',
                ':image'    => $data['image'] ?? '',
                ':id'       => $data['id'] ?? 0,
                ':user_id'  => $userId
            ]);
        }

        public function delete($id, $userId) {
            $stmt = $this->pdo->prepare("DELETE FROM campus_news WHERE id = :id AND user_id = :user_id");
            return $stmt->execute([
                ':id' => $id,
                ':user_id' => $userId
            ]);
        }

        public function getPaginated($limit, $offset) {
            $stmt = $this->pdo->prepare("SELECT * FROM campus_news ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getCount() {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM campus_news");
            return (int)$stmt->fetchColumn();
        }

        public function getAllByUser($userId, $page = 1, $limit = 10) {
            $offset = ($page - 1) * $limit;
            $stmt = $this->pdo->prepare("
                SELECT * FROM campus_news 
                WHERE user_id = :uid 
                ORDER BY created_at DESC 
                LIMIT :limit OFFSET :offset
            ");
            $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function countByUser($userId) {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM campus_news WHERE user_id = :uid");
            $stmt->execute([':uid' => $userId]);
            return (int)$stmt->fetchColumn();
        }

        // Optional Getters
        public function getId()         { return $this->id; }
        public function getTitle()      { return $this->title; }
        public function getBody()       { return $this->body; }
        public function getTags()       { return $this->tags; }
        public function getCategory()   { return $this->category; }
        public function getImage()      { return $this->image; }
        public function getUserId()     { return $this->user_id; }
        public function getCreatedAt()  { return $this->created_at; }
    }
