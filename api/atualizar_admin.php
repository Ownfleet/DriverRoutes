<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$email = trim(strtolower($input['email'] ?? ''));
$active = $input['active'] ?? null;
$createIfNotExists = !empty($input['create_if_not_exists']);

if ($email === '') {
    echo json_encode([
        'ok' => false,
        'error' => 'E-mail não informado.'
    ]);
    exit;
}

if ($active === null) {
    echo json_encode([
        'ok' => false,
        'error' => 'Status active não informado.'
    ]);
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
        return [
            'ok' => false,
            'http_code' => 0,
            'response' => $error
        ];
    }

    curl_close($ch);

    return [
        'ok' => $httpCode >= 200 && $httpCode < 300,
        'http_code' => $httpCode,
        'response' => $response
    ];
}

$check = supabaseRequest(
    'GET',
    SUPABASE_URL . '/rest/v1/admin_users?select=email&email=eq.' . urlencode($email)
);

if (!$check['ok']) {
    echo json_encode([
        'ok' => false,
        'error' => 'Erro ao consultar admin.',
        'response' => $check['response']
    ]);
    exit;
}

$checkData = json_decode($check['response'], true);

if (!empty($checkData)) {
    $update = supabaseRequest(
        'PATCH',
        SUPABASE_URL . '/rest/v1/admin_users?email=eq.' . urlencode($email),
        ['active' => (bool)$active]
    );

    if (!$update['ok']) {
        echo json_encode([
            'ok' => false,
            'error' => 'Erro ao atualizar admin.',
            'response' => $update['response']
        ]);
        exit;
    }

    echo json_encode(['ok' => true]);
    exit;
}

if ($createIfNotExists) {
    $insert = supabaseRequest(
        'POST',
        SUPABASE_URL . '/rest/v1/admin_users',
        [
            'email' => $email,
            'active' => (bool)$active
        ]
    );

    if (!$insert['ok']) {
        echo json_encode([
            'ok' => false,
            'error' => 'Erro ao criar admin.',
            'response' => $insert['response']
        ]);
        exit;
    }

    echo json_encode(['ok' => true]);
    exit;
}

echo json_encode([
    'ok' => false,
    'error' => 'Admin não encontrado.'
]);