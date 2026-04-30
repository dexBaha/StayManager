<?php

require_once __DIR__ . '/../../app/bootstrap.php';
require_once __DIR__ . '/response.php';

$auth = new Auth(new User($db));
$method = $_SERVER['REQUEST_METHOD'];
$data = requestData();
$action = $_GET['action'] ?? $data['action'] ?? '';

if ($method === 'POST' && $action === 'login') {
    $loggedIn = $auth->login($data['email'] ?? '', $data['password'] ?? '');
    jsonResponse([
        'success' => $loggedIn,
        'user' => $loggedIn ? Auth::user() : null,
        'message' => $loggedIn ? 'Logged in.' : 'Invalid email or password.',
    ], $loggedIn ? 200 : 401);
}

if ($method === 'POST' && $action === 'register') {
    $registered = $auth->register($data['name'] ?? '', $data['email'] ?? '', $data['password'] ?? '');
    jsonResponse([
        'success' => $registered,
        'message' => $registered ? 'Account created.' : 'Email already exists.',
    ], $registered ? 201 : 422);
}

if ($method === 'POST' && $action === 'logout') {
    Auth::logout();
    jsonResponse(['success' => true, 'message' => 'Logged out.']);
}

jsonResponse(['error' => 'Invalid API action.'], 400);

