<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . '/../config/db.php';
$pdo = connectDB();

if (!$pdo) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to connect to database."]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($event ?: ["error" => "Event not found"]);
        } else {
            $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            $limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 10;
            $offset = ($page - 1) * $limit;

            $typeFilter = isset($_GET['type']) && $_GET['type'] !== '' ? $_GET['type'] : null;

            if ($typeFilter) {
                $stmt = $pdo->prepare("SELECT * FROM events WHERE LOWER(type) = LOWER(:type) ORDER BY date DESC LIMIT :limit OFFSET :offset");
                $stmt->bindValue(':type', $typeFilter, PDO::PARAM_STR);
                $countStmt = $pdo->prepare("SELECT COUNT(*) FROM events WHERE LOWER(type) = LOWER(:type)");
                $countStmt->bindValue(':type', $typeFilter, PDO::PARAM_STR);
            } else {
                $stmt = $pdo->prepare("SELECT * FROM events ORDER BY date DESC LIMIT :limit OFFSET :offset");
                $countStmt = $pdo->query("SELECT COUNT(*) FROM events");
            }

            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $total = $typeFilter ? $countStmt->execute() && $countStmt->fetchColumn() : $countStmt->fetchColumn();

            echo json_encode([
                "events" => $events,
                "pagination" => [
                    "page" => $page,
                    "limit" => $limit,
                    "total" => (int)$total,
                    "pages" => ceil($total / $limit)
                ]
            ]);
        }
        break;

    case 'POST':
        if (!isset($_POST['title'], $_POST['description'], $_POST['date'], $_POST['type'])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing required fields"]);
            exit();
        }

        $title = htmlspecialchars(trim($_POST['title']));
        $description = htmlspecialchars(trim($_POST['description']));
        $date = $_POST['date'];
        $type = htmlspecialchars(trim($_POST['type']));
        $imagePath = null;

        if (!empty($_FILES['attachment']['name'])) {
            $uploadDir = __DIR__ . '/../uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $filename = uniqid() . '_' . basename($_FILES['attachment']['name']);
            $targetPath = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetPath)) {
                $imagePath = 'uploads/' . $filename;
            }
        }

        $stmt = $pdo->prepare("INSERT INTO events (title, description, date, type, attachment) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $date, $type, $imagePath]);
        echo json_encode(["message" => "Event created", "id" => $pdo->lastInsertId()]);
        break;

    case 'PUT':
        parse_str(file_get_contents("php://input"), $data);
        if (!isset($data['id'], $data['title'], $data['description'], $data['date'], $data['type'])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing required fields"]);
            exit();
        }
        $stmt = $pdo->prepare("UPDATE events SET title = ?, description = ?, date = ?, type = ? WHERE id = ?");
        $stmt->execute([
            htmlspecialchars(trim($data['title'])),
            htmlspecialchars(trim($data['description'])),
            $data['date'],
            htmlspecialchars(trim($data['type'])),
            $data['id']
        ]);
        echo json_encode(["message" => "Event updated"]);
        break;

    case 'DELETE':
        parse_str(file_get_contents("php://input"), $data);
        if (!isset($data['id'])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing event ID"]);
            exit();
        }
        $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
        $stmt->execute([$data['id']]);
        echo json_encode(["message" => "Event deleted"]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
        break;
}
?>
