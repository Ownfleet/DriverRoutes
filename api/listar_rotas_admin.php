<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => SUPABASE_URL . '/rest/v1/view_painel_rotas_admin?select=*',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'apikey: ' . SUPABASE_SERVICE_KEY,
        'Authorization: Bearer ' . SUPABASE_SERVICE_KEY,
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
        'error' => 'Erro ao listar rotas.',
        'http_code' => $httpCode,
        'response' => $response
    ]);
    exit;
}

echo json_encode([
    'ok' => true,
    'data' => json_decode($response, true)
]);