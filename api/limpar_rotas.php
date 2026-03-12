<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

function supabaseRequest($method, $url) {
    $headers = [
        'apikey: ' . SUPABASE_SERVICE_KEY,
        'Authorization: Bearer ' . SUPABASE_SERVICE_KEY,
        'Content-Type: application/json'
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers
    ]);

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

$deleteResp = supabaseRequest(
    'DELETE',
    SUPABASE_URL . '/rest/v1/route_offers?id=gt.0'
);

if (!$deleteResp['ok']) {
    echo json_encode([
        'ok' => false,
        'error' => 'Erro ao limpar rotas.',
        'debug' => $deleteResp
    ]);
    exit;
}

echo json_encode([
    'ok' => true
]);