<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$email = trim(strtolower($input['email'] ?? ''));

if ($email === '') {
    echo json_encode([
        'ok' => false,
        'error' => 'E-mail não informado.'
    ]);
    exit;
}

$url = SUPABASE_URL . '/rest/v1/admin_users?select=email,active&email=eq.' . urlencode($email) . '&active=eq.true';

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'apikey: ' . SUPABASE_ANON_KEY,
        'Authorization: Bearer ' . SUPABASE_ANON_KEY,
        'Content-Type: application/json'
    ]
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    echo json_encode([
        'ok' => false,
        'error' => 'Erro cURL: ' . curl_error($ch)
    ]);
    curl_close($ch);
    exit;
}

curl_close($ch);

if ($httpCode !== 200) {
    echo json_encode([
        'ok' => false,
        'error' => 'Erro ao consultar admin_users.',
        'http_code' => $httpCode,
        'response' => $response
    ]);
    exit;
}

$data = json_decode($response, true);

echo json_encode([
    'ok' => !empty($data)
]);