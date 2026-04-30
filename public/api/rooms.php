<?php

require_once __DIR__ . '/../../app/bootstrap.php';
require_once __DIR__ . '/response.php';

$roomModel = new Room($db);
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $roomId = isset($_GET['id']) ? (int) $_GET['id'] : null;

    if ($roomId) {
        $room = $roomModel->find($roomId);
        jsonResponse($room ? ['room' => $room] : ['error' => 'Room not found'], $room ? 200 : 404);
    }

    jsonResponse(['rooms' => $roomModel->available()]);
}

jsonResponse(['error' => 'Method not allowed'], 405);

