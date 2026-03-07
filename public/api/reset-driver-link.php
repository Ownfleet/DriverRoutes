<?php
session_start();

if (empty($_SESSION['admin_email'])) {
    header('Location: /admin-login.php');
    exit;
}

require_once __DIR__ . '/supabase.php';

$driverId = trim($_POST['driver_id'] ?? '');

if (!$driverId) {
    header('Location: /admin.php?msg=' . urlencode('Informe o Driver ID.'));
    exit;
}

// Apaga o vínculo do motorista na tabela driver_accounts
$res = supabaseRequest(
    'DELETE',
    '/rest/v1/driver_accounts?driver_id=eq.' . urlencode($driverId),
    null,
    true
);

$status = $res['status'] ?? 0;

if ($status >= 200 && $status < 300) {
    header('Location: /admin.php?msg=' . urlencode('Vínculo resetado com sucesso para o ID ' . $driverId));
    exit;
}

header('Location: /admin.php?msg=' . urlencode('Erro ao resetar vínculo.'));
exit;