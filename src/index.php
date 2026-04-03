<?php
require_once __DIR__ . '/../vendor/autoload.php';
header('Content-Type: application/json');

$pdo = new PDO("pgsql:host=" . (getenv('DB_HOST') ?: 'db') . ";dbname=" . (getenv('DB_NAME') ?: 'cdc_demo'), 
                getenv('DB_USER') ?: 'user', getenv('DB_PASSWORD') ?: 'password');

$method = $_SERVER['REQUEST_METHOD'];
$view   = $_GET['view'] ?? '';
$id     = $_GET['id'] ?? null;

if ($view === 'changes') {
    echo json_encode($pdo->query("SELECT * FROM change_log")->fetchAll(PDO::FETCH_ASSOC));
} elseif ($method === 'POST' && !$id) {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!App\Task::validate($data['title'] ?? '')) die(json_encode(['error' => 'Invalid title']));

    $stmt = $pdo->prepare("INSERT INTO tasks (title, status) VALUES (?, ?) RETURNING id");
    $stmt->execute([$data['title'] ?? '', $data['status'] ?? 'open']);
    echo json_encode(['id' => $stmt->fetchColumn()]);
} elseif ($method === 'PUT' && $id) {
    // Handling Updates
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("UPDATE tasks SET title = COALESCE(?, title), status = COALESCE(?, status) WHERE id = ?");
    $stmt->execute([$data['title'] ?? null, $data['status'] ?? null, $id]);
    echo json_encode(['message' => 'Task updated', 'id' => $id]);
} else {
    echo json_encode($pdo->query("SELECT * FROM tasks")->fetchAll(PDO::FETCH_ASSOC));
}
