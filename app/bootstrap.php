<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/Core/Session.php';
require_once __DIR__ . '/Core/helpers.php';
require_once __DIR__ . '/Models/User.php';
require_once __DIR__ . '/Models/Hotel.php';
require_once __DIR__ . '/Models/Room.php';
require_once __DIR__ . '/Models/Reservation.php';
require_once __DIR__ . '/Models/SupportTicket.php';
require_once __DIR__ . '/Models/Dashboard.php';
require_once __DIR__ . '/Services/Auth.php';

Session::start();
$db = Database::connect();
