<?php

require_once __DIR__ . '/../../app/bootstrap.php';
require_once __DIR__ . '/response.php';

Auth::requireAdmin();

$dashboard = new Dashboard($db);

jsonResponse([
    'counts' => $dashboard->counts(),
    'revenue' => $dashboard->revenue(),
    'reservations_by_status' => $dashboard->reservationsByStatus(),
]);

