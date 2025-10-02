<?php
require_once __DIR__ . '/models/Database.php';
require_once __DIR__ . '/controllers/BoardController.php';
require_once __DIR__ . '/controllers/CardController.php';

// Récupération du chemin demandé
$request_uri = $_SERVER['REQUEST_URI'];
$script_name = $_SERVER['SCRIPT_NAME'];

// Extraction du chemin relatif
$path = str_replace(dirname($script_name), '', $request_uri);
$path = parse_url($path, PHP_URL_PATH);
$path = trim($path, '/');

// Routing
switch ($path) {
    case '':
    case 'index.php':
        (new BoardController())->index();
        break;

    case preg_match('#^board/(\d+)$#', $path, $matches) ? $path : false:
        (new BoardController())->show($matches[1]);
        break;

    case 'create-board':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new BoardController())->create($_POST);
        }
        break;

    case 'create-list':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new BoardController())->createList($_POST);
        }
        break;

    case 'create-card':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new CardController())->create($_POST);
        }
        break;

    case 'move-card':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new CardController())->move($_POST);
        }
        break;

    // Routes d'administration de la base de données
    case 'admin/db-stats':
        $db = new Database();
        $stats = $db->getStats();
        header('Content-Type: application/json');
        echo json_encode($stats);
        break;

    case 'admin/db-export':
        require_once __DIR__ . '/database/init.php';
        $exportFile = exportDatabaseToSQL();
        if ($exportFile) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'file' => basename($exportFile)]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Export failed']);
        }
        break;

    case 'admin/db-reset':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = new Database();
            $db->resetDatabase();
            header('Location: /');
            exit;
        }
        break;

    default:
        http_response_code(404);
        echo "Page non trouvée: " . htmlspecialchars($path);
        break;
}
