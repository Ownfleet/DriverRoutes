<?php

require_once __DIR__ . '/admin-guard.php';
require_once __DIR__ . '/supabase.php';

requireAdmin(true);

function voltar(string $msg): void
{
    header('Location: /admin.php?msg=' . urlencode($msg));
    exit;
}

function rotaJaExiste(string $driverId, string $cluster, string $turno, string $dataHoje): bool
{
    $exists = supabaseRequest(
        'GET',
        '/rest/v1/route_offers?driver_id=eq.' . urlencode($driverId)
        . '&cluster=eq.' . urlencode($cluster)
        . '&turno=eq.' . urlencode($turno)
        . '&offer_date=eq.' . urlencode($dataHoje)
        . '&select=id',
        null,
        true
    );

    return (($exists['status'] ?? 0) === 200 && !empty($exists['body']));
}

$dataHoje = date('Y-m-d');

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    $driverId = trim($_POST['driver_id'] ?? '');
    $cluster  = trim($_POST['cluster'] ?? '');
    $turno    = trim($_POST['turno'] ?? '');

    if ($driverId === '' || $cluster === '' || $turno === '') {
        voltar('Preencha Driver ID, Cluster e Turno.');
    }

    $driverRes = supabaseRequest(
        'GET',
        '/rest/v1/drivers?driver_id=eq.' . urlencode($driverId) . '&select=driver_id',
        null,
        true
    );

    if (($driverRes['status'] ?? 0) !== 200 || empty($driverRes['body'])) {
        voltar('Driver ID não encontrado na base de motoristas.');
    }

    if (rotaJaExiste($driverId, $cluster, $turno, $dataHoje)) {
        voltar('Essa rota já foi enviada para esse motorista hoje.');
    }

    $insert = supabaseRequest(
        'POST',
        '/rest/v1/route_offers',
        [[
            'driver_id' => $driverId,
            'cluster' => $cluster,
            'turno' => $turno,
            'offer_date' => $dataHoje,
            'status' => 'pendente'
        ]],
        true
    );

    $status = $insert['status'] ?? 0;

    if ($status >= 200 && $status < 300) {
        voltar('Rota manual criada com sucesso.');
    }

    voltar('Erro ao criar rota manual.');
}

$tmpPath = $_FILES['file']['tmp_name'] ?? '';
$originalName = $_FILES['file']['name'] ?? '';

$ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
if ($ext !== 'csv') {
    voltar('Envie um arquivo CSV.');
}

$handle = fopen($tmpPath, 'r');
if (!$handle) {
    voltar('Não foi possível ler o arquivo CSV.');
}

$header = fgetcsv($handle);
if (!$header) {
    fclose($handle);
    voltar('CSV vazio ou inválido.');
}

$header = array_map(fn($h) => strtolower(trim($h)), $header);

$idxDriver = array_search('driver_id', $header);
$idxCluster = array_search('cluster', $header);
$idxTurno = array_search('turno', $header);

if ($idxDriver === false || $idxCluster === false || $idxTurno === false) {
    fclose($handle);
    voltar('O CSV precisa ter as colunas: driver_id, cluster, turno.');
}

$inseridas = 0;
$duplicadas = 0;
$erros = 0;

while (($row = fgetcsv($handle)) !== false) {
    $driverId = trim($row[$idxDriver] ?? '');
    $cluster  = trim($row[$idxCluster] ?? '');
    $turno    = trim($row[$idxTurno] ?? '');

    if ($driverId === '' || $cluster === '' || $turno === '') {
        $erros++;
        continue;
    }

    $driverRes = supabaseRequest(
        'GET',
        '/rest/v1/drivers?driver_id=eq.' . urlencode($driverId) . '&select=driver_id',
        null,
        true
    );

    if (($driverRes['status'] ?? 0) !== 200 || empty($driverRes['body'])) {
        $erros++;
        continue;
    }

    if (rotaJaExiste($driverId, $cluster, $turno, $dataHoje)) {
        $duplicadas++;
        continue;
    }

    $insert = supabaseRequest(
        'POST',
        '/rest/v1/route_offers',
        [[
            'driver_id' => $driverId,
            'cluster' => $cluster,
            'turno' => $turno,
            'offer_date' => $dataHoje,
            'status' => 'pendente'
        ]],
        true
    );

    $status = $insert['status'] ?? 0;

    if ($status >= 200 && $status < 300) {
        $inseridas++;
    } else {
        $erros++;
    }
}

fclose($handle);

voltar("Importação concluída. Inseridas: {$inseridas}. Duplicadas ignoradas: {$duplicadas}. Erros: {$erros}.");