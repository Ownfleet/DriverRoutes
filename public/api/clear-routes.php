<?php
session_start();

if (empty($_SESSION['admin_email'])) {
    header('Location: /admin-login.php');
    exit;
}

require_once __DIR__ . '/supabase.php';

// apaga todas as rotas da tabela
$res = supabaseRequest(
    'DELETE',
    '/rest/v1/route_offers?id=gt.0',
    null,
    true
);

$status = $res['status'] ?? 0;

if ($status >= 200 && $status < 300) {
    header('Location: /admin.php?msg=' . urlencode('Base de rotas limpa com sucesso.'));
    exit;
}

header('Location: /admin.php?msg=' . urlencode('Erro ao limpar a base de rotas.'));
exit;