<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/supabase.php';

$data = json_decode(file_get_contents('php://input'), true);

$jwt = $data['access_token'] ?? '';
$driverId = trim($data['driver_id'] ?? '');
$mode = $data['mode'] ?? 'preview';

if (!$jwt) {
    echo json_encode([
        'ok' => false,
        'message' => 'Token ausente'
    ]);
    exit;
}

if (!$driverId) {
    echo json_encode([
        'ok' => false,
        'message' => 'ID ausente'
    ]);
    exit;
}

/**
 * 1) Validar usuário logado pelo token
 */
$userRes = supabaseRequest('GET', '/auth/v1/user', null, false, $jwt);

if (($userRes['status'] ?? 0) !== 200 || empty($userRes['body']['id'])) {
    echo json_encode([
        'ok' => false,
        'message' => 'Usuário não autenticado',
        'debug_auth_status' => $userRes['status'] ?? null,
        'debug_auth_body' => $userRes['body'] ?? null
    ]);
    exit;
}

$userId = $userRes['body']['id'];

/**
 * 2) Verificar se a conta Google já está vinculada
 */
$alreadyLinked = supabaseRequest(
    'GET',
    '/rest/v1/driver_accounts?user_id=eq.' . urlencode($userId) . '&select=*',
    null,
    true
);

if (($alreadyLinked['status'] ?? 0) === 200 && !empty($alreadyLinked['body'])) {
    echo json_encode([
        'ok' => false,
        'message' => 'Sua conta já está vinculada a um ID.'
    ]);
    exit;
}

/**
 * 3) Procurar o motorista pelo driver_id
 */
$driverRes = supabaseRequest(
    'GET',
    '/rest/v1/drivers?driver_id=eq.' . urlencode($driverId) . '&select=*',
    null,
    true
);

if (($driverRes['status'] ?? 0) !== 200 || empty($driverRes['body'])) {
    echo json_encode([
        'ok' => false,
        'message' => 'ID não encontrado.',
        'debug_driver_status' => $driverRes['status'] ?? null,
        'debug_driver_body' => $driverRes['body'] ?? null
    ]);
    exit;
}

$driver = $driverRes['body'][0];

/**
 * 4) Verificar se esse ID já está vinculado a outra conta
 */
$driverUsed = supabaseRequest(
    'GET',
    '/rest/v1/driver_accounts?driver_id=eq.' . urlencode($driverId) . '&select=*',
    null,
    true
);

if (($driverUsed['status'] ?? 0) === 200 && !empty($driverUsed['body'])) {
    echo json_encode([
        'ok' => false,
        'message' => 'Este ID já está vinculado a outra conta.'
    ]);
    exit;
}

/**
 * 5) Se for só visualização, retorna os dados
 */
if ($mode === 'preview') {
    echo json_encode([
        'ok' => true,
        'driver' => $driver
    ]);
    exit;
}

/**
 * 6) Confirmar vínculo
 */
$insertRes = supabaseRequest(
    'POST',
    '/rest/v1/driver_accounts',
    [[
        'user_id' => $userId,
        'driver_id' => $driverId,
        'bloqueado' => true
    ]],
    true
);

if (($insertRes['status'] ?? 0) >= 200 && ($insertRes['status'] ?? 0) < 300) {
    echo json_encode([
        'ok' => true,
        'message' => 'Conta vinculada com sucesso.'
    ]);
} else {
    echo json_encode([
        'ok' => false,
        'message' => 'Erro ao vincular conta.',
        'debug_insert_status' => $insertRes['status'] ?? null,
        'debug_insert_body' => $insertRes['body'] ?? null
    ]);
}