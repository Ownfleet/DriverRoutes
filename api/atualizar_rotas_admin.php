<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$routeId = (int)($input['route_id'] ?? 0);
$action = trim((string)($input['action'] ?? ''));
$newDriverCode = trim((string)($input['new_driver_code'] ?? ''));

if ($routeId <= 0) {
    echo json_encode(['ok' => false, 'error' => 'route_id inválido.']);
    exit;
}

if ($action === '') {
    echo json_encode(['ok' => false, 'error' => 'Ação não informada.']);
    exit;
}

function supabaseRequest($method, $url, $body = null) {
    $headers = [
        'apikey: ' . SUPABASE_SERVICE_KEY,
        'Authorization: Bearer ' . SUPABASE_SERVICE_KEY,
        'Content-Type: application/json'
    ];

    $ch = curl_init();
    $opts = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers
    ];

    if ($body !== null) {
        $opts[CURLOPT_POSTFIELDS] = json_encode($body);
    }

    curl_setopt_array($ch, $opts);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return ['ok' => false, 'http_code' => 0, 'response' => $error];
    }

    curl_close($ch);

    return [
        'ok' => $httpCode >= 200 && $httpCode < 300,
        'http_code' => $httpCode,
        'response' => $response
    ];
}

if ($action === 'cancel') {
    $update = supabaseRequest(
        'PATCH',
        SUPABASE_URL . '/rest/v1/route_offers?id=eq.' . $routeId,
        ['status' => 'cancelled']
    );

    if (!$update['ok']) {
        echo json_encode(['ok' => false, 'error' => 'Erro ao cancelar rota.', 'response' => $update['response']]);
        exit;
    }

    echo json_encode(['ok' => true]);
    exit;
}

if ($action === 'reassign') {
    if ($newDriverCode === '') {
        echo json_encode(['ok' => false, 'error' => 'Novo driver_code não informado.']);
        exit;
    }

    $driver = supabaseRequest(
        'GET',
        SUPABASE_URL . '/rest/v1/drivers?select=id,full_name,shift,active&driver_code=eq.' . urlencode($newDriverCode) . '&active=eq.true&limit=1'
    );

    if (!$driver['ok']) {
        echo json_encode(['ok' => false, 'error' => 'Erro ao buscar novo motorista.', 'response' => $driver['response']]);
        exit;
    }

    $driverData = json_decode($driver['response'], true);
    $novoMotorista = $driverData[0] ?? null;

    if (!$novoMotorista) {
        echo json_encode(['ok' => false, 'error' => 'Motorista não encontrado para repasse.']);
        exit;
    }

    $update = supabaseRequest(
        'PATCH',
        SUPABASE_URL . '/rest/v1/route_offers?id=eq.' . $routeId,
        [
            'driver_id' => $novoMotorista['id'],
            'shift' => $novoMotorista['shift'] ?? null,
            'imported_name' => $novoMotorista['full_name'] ?? null,
            'status' => 'pending',
            'responded_at' => null,
            'sent_at' => gmdate('c')
        ]
    );

    if (!$update['ok']) {
        echo json_encode(['ok' => false, 'error' => 'Erro ao repassar rota.', 'response' => $update['response']]);
        exit;
    }

    echo json_encode(['ok' => true]);
    exit;
}

echo json_encode(['ok' => false, 'error' => 'Ação inválida.']);