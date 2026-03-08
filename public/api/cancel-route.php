<?php

require_once __DIR__ . '/admin-guard.php';
require_once __DIR__ . '/supabase.php';

requireAdmin(true);

$routeId = (int)($_POST['route_id'] ?? 0);

if ($routeId <= 0) {
    header('Location: /admin.php?msg=' . urlencode('Rota inválida.'));
    exit;
}

$routeRes = supabaseRequest(
    'GET',
    '/rest/v1/route_offers?id=eq.' . $routeId . '&select=*',
    null,
    true
);

if (($routeRes['status'] ?? 0) !== 200 || empty($routeRes['body'])) {
    header('Location: /admin.php?msg=' . urlencode('Rota não encontrada.'));
    exit;
}

$route = $routeRes['body'][0];
$statusAtual = $route['status'] ?? '';

if ($statusAtual !== 'pendente') {
    header('Location: /admin.php?msg=' . urlencode('Só é possível cancelar rotas pendentes.'));
    exit;
}

$updateRes = supabaseRequest(
    'PATCH',
    '/rest/v1/route_offers?id=eq.' . $routeId,
    ['status' => 'cancelada'],
    true
);

if (($updateRes['status'] ?? 0) >= 200 && ($updateRes['status'] ?? 0) < 300) {
    header('Location: /admin.php?msg=' . urlencode('Rota cancelada com sucesso.'));
    exit;
}

header('Location: /admin.php?msg=' . urlencode('Erro ao cancelar rota.'));
exit;