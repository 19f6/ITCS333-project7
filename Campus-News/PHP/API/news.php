<?php
header('Content-Type: application/json');

require_once(__DIR__ . '/../../../backend/config/db.php');
require_once(__DIR__ . '/../../../backend/models/CampusNews.php');

$pdo = connectDB();
$newsModel = new CampusNews($pdo);
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $news = $newsModel->getById($id);

            if ($news) {
                $news['fromJson'] = false;
                echo json_encode($news);
            } else {
                echo json_encode([]);
            }
        } else {
            $dbNews = array_map(function ($item) {
                $item['fromJson'] = false;
                return $item;
            }, $newsModel->getAll());

            echo json_encode($dbNews);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $userId = 0;
        echo json_encode($newsModel->create($data, $userId) ? ['message' => 'News created'] : ['error' => 'Failed']);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $userId = 0;
        echo json_encode($newsModel->update($data, $userId) ? ['message' => 'Updated'] : ['error' => 'Update failed']);
        break;

    case 'DELETE':
        parse_str(file_get_contents("php://input"), $data);
        $userId = 0;
        echo json_encode($newsModel->delete($data['id'] ?? 0, $userId) ? ['message' => 'Deleted'] : ['error' => 'Failed']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
}
