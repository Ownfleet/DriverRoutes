<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$rotas = $input['rotas'] ?? [];

if (!is_array($rotas) || count($rotas) === 0) {
    echo json_encode([
        'ok' => false,
        'error' => 'Nenhuma rota enviada.'
    ]);
    exit;
}

function supabase_request(string $method, string $url, ?array $body = null): array
{
    $headers = [
        'apikey: ' . SUPABASE_SERVICE_KEY,
        'Authorization: Bearer ' . SUPABASE_SERVICE_KEY,
        'Content-Type: application/json'
    ];

    $ch = curl_init();

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers
    ];

    if ($body !== null) {
        $options[CURLOPT_POSTFIELDS] = json_encode($body);
    }

    curl_setopt_array($ch, $options);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);

    curl_close($ch);

    if ($curlError) {
        return [
            'ok' => false,
            'http_code' => 0,
            'raw' => $curlError,
            'data' => null
        ];
    }

    return [
        'ok' => $httpCode >= 200 && $httpCode < 300,
        'http_code' => $httpCode,
        'raw' => $response,
        'data' => json_decode($response, true)
    ];
}

$nao_encontrados = [];
$erros_insert = [];
$total = 0;

foreach ($rotas as $rota) {
    $driver_code = trim((string)($rota['driver_code'] ?? ''));
    $cluster = trim((string)($rota['cluster'] ?? ''));

    if ($driver_code === '' || $cluster === '') {
        continue;
    }

    $urlBusca = SUPABASE_URL . '/rest/v1/drivers?select=id,driver_code,full_name,shift,active&driver_code=eq.' . urlencode($driver_code) . '&active=eq.true&limit=1';
    $busca = supabase_request('GET', $urlBusca);

    if (!$busca['ok']) {
        echo json_encode([
            'ok' => false,
            'error' => 'Erro ao consultar a tabela drivers.',
            'http_code' => $busca['http_code'],
            'response' => $busca['data'] ?: $busca['raw']
        ]);
        exit;
    }

    $driver = $busca['data'][0] ?? null;

    if (!$driver) {
        $nao_encontrados[] = $driver_code;
        continue;
    }

    $insertBody = [
        'driver_id' => $driver['id'],
        'cluster' => $cluster,
        'shift' => $driver['shift'] ?? null,
        'status' => 'pending',
        'imported_name' => $driver['full_name'] ?? null,
        'sent_at' => gmdate('c')
    ];

    $insert = supabase_request('POST', SUPABASE_URL . '/rest/v1/route_offers', $insertBody);

    if (!$insert['ok']) {
        $erros_insert[] = [
            'driver_code' => $driver_code,
            'cluster' => $cluster,
            'http_code' => $insert['http_code'],
            'response' => $insert['data'] ?: $insert['raw']
        ];
        continue;
    }

    $total++;
}

echo json_encode([
    'ok' => $total > 0,
    'total' => $total,
    'nao_encontrados' => $nao_encontrados,
    'erros_insert' => $erros_insert,
    'error' => $total > 0 ? null : 'Nenhuma rota válida foi salva.'
]);