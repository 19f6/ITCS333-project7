<?php

class CourseReview {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function createCourseReview($user_id, $college, $course, $description, $rating) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO course_reviews (user_id, college, course, description, rating)
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
                FROM course_reviews r
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
                FROM course_reviews r
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
                FROM course_reviews r
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
            $stmt = $this->pdo->prepare("DELETE FROM course_reviews WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error deleting course review: " . $e->getMessage());
            return false;
        }
    }
}
?>
