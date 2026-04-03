<?php
require_once __DIR__ . '/../vendor/autoload.php';
#header('Content-Type: application/json');

$pdo = new PDO(
    "pgsql:host=" . (getenv('DB_HOST') ?: 'db') . ";dbname=" . (getenv('DB_NAME') ?: 'cdc_demo'),
    getenv('DB_USER') ?: 'user',
    getenv('DB_PASSWORD') ?: 'password'
);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET' && $uri === '/') {
    echo json_encode($pdo->query("SELECT * FROM tasks"));
} elseif ($method === 'POST' && $uri === '/') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!App\Task::validate($data['title'] ?? ''))
        die(json_encode(['error' => 'Invalid title']));

    $stmt = $pdo->prepare("INSERT INTO tasks (title, status) VALUES (?, ?) RETURNING id");
    $stmt->execute([$data['title'] ?? '', $data['status'] ?? 'open']);
    echo json_encode(['id' => $stmt->fetchColumn()]);
} elseif ($uri === '/changes') {
    echo json_encode($pdo->query("SELECT * FROM change_log")->fetchAll(PDO::FETCH_ASSOC));
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Not Found']);
}
