<?php

class GroupComment {
    private $pdo;
    private $id;
    private $user_id;
    private $group_id;
    private $comment_content;
    private $created_at;

    public function __construct($pdo, $id = null, $user_id = null, $group_id = null, $comment_content = null, $created_at = null) {
        $this->pdo = $pdo;
        $this->id = $id;
        $this->user_id = $user_id;
        $this->group_id = $group_id;
        $this->comment_content = $comment_content;
        $this->created_at = $created_at;
    }


    public function createComment($user_id, $group_id, $comment_content) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO group_comments (user_id, group_id, comment_content) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $group_id, $comment_content]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error adding comment: " . $e->getMessage());
            return false;
        }
    }

    //get all comments for group
    public function getCommentsByGroupId($group_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT gc.*, u.name as user_name
                FROM group_comments gc
                JOIN users u ON gc.user_id = u.id
                WHERE gc.group_id = ?
                ORDER BY gc.created_at DESC
            ");
            $stmt->execute([$group_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching comments: " . $e->getMessage());
            return [];
        }
    }


    public function deleteComment($comment_id, $user_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT user_id FROM group_comments WHERE id = ?");
            $stmt->execute([$comment_id]);
            $comment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$comment || $comment['user_id'] != $user_id) {
                return false; // not the creator of the comment
            }
            
            $stmt = $this->pdo->prepare("DELETE FROM group_comments WHERE id = ?");
            return $stmt->execute([$comment_id]);
        } catch (PDOException $e) {
            error_log("Error deleting comment: " . $e->getMessage());
            return false;
        }
    }


    public function getCommentById($comment_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT gc.*, u.name as user_name
                FROM group_comments gc
                JOIN users u ON gc.user_id = u.id
                WHERE gc.id = ?
            ");
            $stmt->execute([$comment_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching comment: " . $e->getMessage());
            return false;
        }
    }

    // getters
    public function getId() {
        return $this->id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function getGroupId() {
        return $this->group_id;
    }

    public function getCommentContent() {
        return $this->comment_content;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }
}
?>