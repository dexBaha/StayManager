<?php

require_once __DIR__ . '/../../app/bootstrap.php';
require_once __DIR__ . '/response.php';

$reservationModel = new Reservation($db);
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    Auth::requireLogin();
    $user = Auth::user();

    if (Auth::isAdmin()) {
        jsonResponse(['reservations' => $reservationModel->all()]);
    }

    jsonResponse(['reservations' => $reservationModel->forUser($user['id'])]);
}

if ($method === 'POST') {
    Auth::requireLogin();
    $data = requestData();

    $created = $reservationModel->create(
        Auth::user()['id'],
        (int) ($data['room_id'] ?? 0),
        trim($data['check_in'] ?? ''),
        trim($data['check_out'] ?? '')
    );

    jsonResponse(
        ['success' => $created, 'message' => $created ? 'Reservation created.' : 'Invalid reservation data.'],
        $created ? 201 : 422
    );
}

if ($method === 'DELETE') {
    Auth::requireLogin();
    $data = requestData();
    $reservationId = (int) ($_GET['id'] ?? $data['id'] ?? 0);
    $userId = Auth::isAdmin() ? null : Auth::user()['id'];
    $deleted = $reservationModel->delete($reservationId, $userId);

    jsonResponse(['success' => $deleted, 'message' => $deleted ? 'Reservation deleted.' : 'Reservation not found.']);
}

jsonResponse(['error' => 'Method not allowed'], 405);
