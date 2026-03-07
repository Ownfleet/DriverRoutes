<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['admin_email'])) {
    echo json_encode([
        'ok' => false,
        'message' => 'Não autorizado'
    ]);
    exit;
}

require_once __DIR__ . '/supabase.php';

$status = trim($_GET['status'] ?? '');
$driverId = trim($_GET['driver_id'] ?? '');

$query = '/rest/v1/route_offers?select=*&order=created_at.desc';

if ($status !== '' && in_array($status, ['pendente', 'aceita', 'recusada', 'cancelada', 'expirada'])) {
    $query .= '&status=eq.' . urlencode($status);
}

if ($driverId !== '') {
    $query .= '&driver_id=eq.' . urlencode($driverId);
}

$res = supabaseRequest('GET', $query, null, true);

if (($res['status'] ?? 0) >= 200 && ($res['status'] ?? 0) < 300) {
    echo json_encode([
        'ok' => true,
        'routes' => $res['body'] ?? []
    ]);
    exit;
}

echo json_encode([
    'ok' => false,
    'message' => 'Erro ao buscar rotas'
]);