<?php

class CourseReview {
    private $pdo;
    private $id;
    private $user_id;
    private $college;
    private $course;
    private $description;
    private $rating;
    private $created_at;

    public function __construct($pdo, $id = null, $user_id = null, $college = null, $course = null, $description = null, $rating = null, $created_at = null) {
        $this->pdo = $pdo;
        $this->id = $id;
        $this->user_id = $user_id;
        $this->college = $college;
        $this->course = $course;
        $this->description = $description;
        $this->rating = $rating;
        $this->created_at = $created_at;
    }

    public function createCourseReview($user_id, $college, $course, $description, $rating) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO coursereviews_reviews (user_id, college, course, description, rating)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$user_id, $college, $course, $description, $rating]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error creating course review: " . $e->getMessage());
            return false;
        }
    }

    public function fetchAllCourseReviews() {
        try {
            $query = "
                SELECT r.*, u.name as user_name, u.email as user_email
                FROM coursereviews_reviews r
                JOIN users u ON r.user_id = u.id
                ORDER BY r.id DESC
            ";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching course reviews: " . $e->getMessage());
            return [];
        }
    }

    public function getCourseReviewById($id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT r.*, u.name as user_name, u.email as user_email
                FROM coursereviews_reviews r
                JOIN users u ON r.user_id = u.id
                WHERE r.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching course review by ID: " . $e->getMessage());
            return false;
        }
    }

    public function getCourseReviewsByUserId($user_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT r.*, u.name as user_name, u.email as user_email
                FROM coursereviews_reviews r
                JOIN users u ON r.user_id = u.id
                WHERE r.user_id = ?
                ORDER BY r.id DESC
            ");
            $stmt->execute([$user_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching course reviews by user ID: " . $e->getMessage());
            return [];
        }
    }

    public function deleteCourseReview($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM coursereviews_reviews WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error deleting course review: " . $e->getMessage());
            return false;
        }
    }

    // Getters
    public function getId() { return $this->id; }
    public function getUserId() { return $this->user_id; }
    public function getCollege() { return $this->college; }
    public function getCourse() { return $this->course; }
    public function getDescription() { return $this->description; }
    public function getRating() { return $this->rating; }
    public function getCreatedAt() { return $this->created_at; }
}
?>
