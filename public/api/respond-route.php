<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/supabase.php';

$data = json_decode(file_get_contents('php://input'), true);

$jwt = $data['access_token'] ?? '';
$routeId = (int)($data['route_id'] ?? 0);
$status = trim($data['status'] ?? '');

if (!$jwt) {
    echo json_encode(['ok' => false, 'message' => 'Token ausente.']);
    exit;
}

if ($routeId <= 0) {
    echo json_encode(['ok' => false, 'message' => 'Rota inválida.']);
    exit;
}

if (!in_array($status, ['aceita', 'recusada'])) {
    echo json_encode(['ok' => false, 'message' => 'Status inválido.']);
    exit;
}

// usuário autenticado
$userRes = supabaseRequest('GET', '/auth/v1/user', null, false, $jwt);

if (($userRes['status'] ?? 0) !== 200 || empty($userRes['body']['id'])) {
    echo json_encode(['ok' => false, 'message' => 'Usuário não autenticado.']);
    exit;
}

$userId = $userRes['body']['id'];

// vínculo user -> driver_id
$linkRes = supabaseRequest(
    'GET',
    '/rest/v1/driver_accounts?user_id=eq.' . urlencode($userId) . '&select=*',
    null,
    true
);

if (($linkRes['status'] ?? 0) !== 200 || empty($linkRes['body'])) {
    echo json_encode(['ok' => false, 'message' => 'Conta não vinculada a nenhum motorista.']);
    exit;
}

$driverId = $linkRes['body'][0]['driver_id'] ?? '';

if (!$driverId) {
    echo json_encode(['ok' => false, 'message' => 'Driver ID não encontrado para esta conta.']);
    exit;
}

// busca rota
$routeRes = supabaseRequest(
    'GET',
    '/rest/v1/route_offers?id=eq.' . $routeId . '&select=*',
    null,
    true
);

if (($routeRes['status'] ?? 0) !== 200 || empty($routeRes['body'])) {
    echo json_encode(['ok' => false, 'message' => 'Rota não encontrada.']);
    exit;
}

$route = $routeRes['body'][0];

// garante que a rota pertence ao motorista
if (($route['driver_id'] ?? '') !== $driverId) {
    echo json_encode(['ok' => false, 'message' => 'Esta rota não pertence a você.']);
    exit;
}

// só pode responder rota pendente
$statusAtual = $route['status'] ?? '';
if ($statusAtual !== 'pendente') {
    echo json_encode([
        'ok' => false,
        'message' => 'Essa rota já foi finalizada. Status atual: ' . $statusAtual
    ]);
    exit;
}

// atualiza
$updateRes = supabaseRequest(
    'PATCH',
    '/rest/v1/route_offers?id=eq.' . $routeId,
    ['status' => $status],
    true
);

if (($updateRes['status'] ?? 0) >= 200 && ($updateRes['status'] ?? 0) < 300) {
    echo json_encode(['ok' => true, 'message' => 'Resposta enviada com sucesso.']);
    exit;
}

echo json_encode(['ok' => false, 'message' => 'Erro ao responder rota.']);