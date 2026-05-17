<?php
require_once __DIR__ . '/../app/bootstrap.php';
Auth::requireLogin();

$reservationModel = new Reservation($db);
$reservation = $reservationModel->findDetailed((int) ($_GET['reservation_id'] ?? 0));
$user = Auth::user();

if (!$reservation) {
    redirect('/dashboard.php');
}

if (!Auth::isAdmin() && (int) $reservation['user_id'] !== (int) $user['id']) {
    redirect('/dashboard.php');
}

if ($reservation['status'] !== 'confirmed') {
    Session::flash('success', 'Invoice is available after the admin confirms your reservation.');
    redirect('/dashboard.php');
}

if (($reservation['payment_status'] ?? '') !== 'paid') {
    Session::flash('success', 'Please complete payment before downloading the invoice.');
    redirect('/dashboard.php');
}

$invoiceUrl = (new InvoiceService())->generate($reservation);
redirect($invoiceUrl);
