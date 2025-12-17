<?php

declare(strict_types=1);

header('Content-Type: application/json');
require_once __DIR__ . '/../services/ProductService.php';

$service = new ProductService();
$method = $_SERVER['REQUEST_METHOD'];

$data = json_decode(file_get_contents('php://input'), true) ?? [];

try {
    if ($method === 'GET') {
        $filters = [
            'name' => $_GET['name'] ?? '',
            'category' => $_GET['category'] ?? '',
            'min_price' => $_GET['min_price'] ?? '',
            'max_price' => $_GET['max_price'] ?? '',
            'min_quantity' => $_GET['min_quantity'] ?? '',
            'max_quantity' => $_GET['max_quantity'] ?? '',
            'sort_field' => $_GET['sort_field'] ?? '',
            'sort_dir' => $_GET['sort_dir'] ?? '',
        ];
        $products = $service->filter($filters);
        echo json_encode($products);
    } elseif ($method === 'POST') {
        $service->create($data['name'], $data['category_id'], $data['price'], $data['quantity']);
        echo json_encode(['success'=>true]);
    } elseif ($method === 'PUT') {
        $service->update($data['id'], $data['name'], $data['category_id'], $data['price'], $data['quantity']);
        echo json_encode(['success'=>true]);
    } elseif ($method === 'DELETE') {
        $service->delete($data['id']);
        echo json_encode(['success'=>true]);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error'=>$e->getMessage()]);
}
