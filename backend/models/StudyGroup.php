<?php

class StudyGroup {
    private $pdo;
    private $id;
    private $user_id;
    private $title;
    private $college;
    private $course;
    private $description;
    private $location;
    private $location_details;
    private $max_members;
    private $created_at;

    public function __construct($pdo, $id = null, $user_id = null, $title = null, $college = null, $course = null, $description = null, $location = null, $location_details = null, $max_members = null, $created_at = null) {
        $this->pdo = $pdo;
        $this->id = $id;
        $this->user_id = $user_id;
        $this->title = $title;
        $this->college = $college;
        $this->course = $course;
        $this->description = $description;
        $this->location = $location;
        $this->location_details = $location_details;
        $this->max_members = $max_members;
        $this->created_at = $created_at;
    }

    public function createStudyGroup($user_id, $title, $college = null, $course = null, $description = null, $location = null, $location_details = null, $max_members = null) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO study_groups (user_id, title, college, course, description, location, location_details, max_members) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $title, $college, $course, $description, $location, $location_details, $max_members]);
            $groupId = $this->pdo->lastInsertId();
            
            if ($groupId) {
                $this->addMemberToGroup($user_id, $groupId);
            }
            
            return $groupId;
        } catch (PDOException $e) {
            error_log("Error creating study group: " . $e->getMessage());
            return false;
        }
    }


    public function fetchAllStudyGroups() {
        try {
            $query = "SELECT sg.*, u.name as user_name, u.email as user_email, 
                     (SELECT COUNT(*) FROM group_members gm WHERE gm.group_id = sg.id) as member_count 
                     FROM study_groups sg 
                     JOIN users u ON sg.user_id = u.id 
                     ORDER BY sg.created_at DESC";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching study groups with member counts: " . $e->getMessage());
            return [];
        }
    }


    public function getGroupById($id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT sg.*, u.name as user_name, u.email as user_email,
                (SELECT COUNT(*) FROM group_members gm WHERE gm.group_id = sg.id) as member_count
                FROM study_groups sg 
                JOIN users u ON sg.user_id = u.id 
                WHERE sg.id = ?
            ");
            $stmt->execute([$id]);
            $group = $stmt->fetch(PDO::FETCH_ASSOC);
            return $group ? $group : false;
        } catch (PDOException $e) {
            error_log("Error fetching study group by ID: " . $e->getMessage());
            return false;
        }
    }


    public function getGroupsByUserId($user_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT sg.*, u.name as user_name, u.email as user_email,
                (SELECT COUNT(*) FROM group_members gm WHERE gm.group_id = sg.id) as member_count
                FROM study_groups sg 
                JOIN users u ON sg.user_id = u.id 
                WHERE sg.user_id = ? 
                ORDER BY sg.created_at DESC
            ");
            $stmt->execute([$user_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching study groups by user ID: " . $e->getMessage());
            return [];
        }
    }

    public function updateStudyGroup($id, $title, $college = null, $course = null, $description = null, $location = null, $location_details = null, $max_members = null) {
        try {
            $stmt = $this->pdo->prepare("UPDATE study_groups SET title = ?, college = ?, course = ?, description = ?, location = ?, location_details = ?, max_members = ? WHERE id = ?");
            return $stmt->execute([$title, $college, $course, $description, $location, $location_details, $max_members, $id]);
        } catch (PDOException $e) {
            error_log("Error updating study group: " . $e->getMessage());
            return false;
        }
    }


    public function deleteStudyGroup($id) {
        try {
            //delete all members of the group
            $stmt = $this->pdo->prepare("DELETE FROM group_members WHERE group_id = ?");
            $stmt->execute([$id]);
            
            // then delete the group 
            $stmt = $this->pdo->prepare("DELETE FROM study_groups WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error deleting study group: " . $e->getMessage());
            return false;
        }
    }
    

    public function addMemberToGroup($user_id, $group_id) {
        try {
            // Check if the group exists
            $groupStmt = $this->pdo->prepare("SELECT id, max_members FROM study_groups WHERE id = ?");
            $groupStmt->execute([$group_id]);
            $group = $groupStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$group) {
                return false; // Group doesn't exist
            }
            
            $checkStmt = $this->pdo->prepare("SELECT id FROM group_members WHERE user_id = ? AND group_id = ?");
            $checkStmt->execute([$user_id, $group_id]);
            if ($checkStmt->rowCount() > 0) {
                return true; // User is already a member
            }
            
            // check if the group is full
            $countStmt = $this->pdo->prepare("SELECT COUNT(*) as member_count FROM group_members WHERE group_id = ?");
            $countStmt->execute([$group_id]);
            $countResult = $countStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($countResult['member_count'] >= $group['max_members']) {
                return false; // group is full
            }
            
            // Add user to the group
            $stmt = $this->pdo->prepare("INSERT INTO group_members (user_id, group_id) VALUES (?, ?)");
            return $stmt->execute([$user_id, $group_id]);
        } catch (PDOException $e) {
            error_log("Error adding member to group: " . $e->getMessage());
            return false;
        }
    }
    
    public function removeMemberFromGroup($user_id, $group_id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM group_members WHERE user_id = ? AND group_id = ?");
            return $stmt->execute([$user_id, $group_id]);
        } catch (PDOException $e) {
            error_log("Error removing member from group: " . $e->getMessage());
            return false;
        }
    }
    
    public function getGroupMembers($group_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT u.id, u.name, u.email, gm.joined_at
                FROM group_members gm
                JOIN users u ON gm.user_id = u.id
                WHERE gm.group_id = ?
                ORDER BY gm.joined_at ASC
            ");
            $stmt->execute([$group_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting group members: " . $e->getMessage());
            return [];
        }
    }

    // getters
    public function getId() {
        return $this->id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getCollege() {
        return $this->college;
    }

    public function getCourse() {
        return $this->course;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getLocation() {
        return $this->location;
    }

    public function getLocationDetails() {
        return $this->location_details;
    }
    
    public function getMaxMembers() {
        return $this->max_members;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }
}

?>