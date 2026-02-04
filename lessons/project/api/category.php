<?php

declare(strict_types=1);

require_once __DIR__ . '/../spl_autoload_register.php';

use App\Services\CategoryService;
use App\Database\Connection;
use App\Database\Schema;

header('Content-Type: application/json');

Schema::createTables(Connection::getInstance());
$service = new CategoryService();
$method = $_SERVER['REQUEST_METHOD'];

$data = json_decode(file_get_contents('php://input'), true) ?? [];

try {
    if ($method === 'GET') {
        echo json_encode($service->getCategories());
    } elseif ($method === 'POST') {
        $cat = $service->createCategory($data['name']);
        echo json_encode($cat);
    } elseif ($method === 'PUT') {
        $service->updateCategory($data['id'], $data['name']);
        echo json_encode(['success'=>true]);
    } elseif ($method === 'DELETE') {
        $service->deleteCategory($data['id']);
        echo json_encode(['success'=>true]);
    }
} catch (\Exception $e) {
    http_response_code(400);
    echo json_encode(['error'=>$e->getMessage()]);
}
