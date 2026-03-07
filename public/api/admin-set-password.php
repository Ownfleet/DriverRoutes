<?php
require_once __DIR__ . '/supabase.php';

$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';
$confirmar = $_POST['confirmar_senha'] ?? '';

if (!$email || !$senha || !$confirmar) {
    exit('Preencha todos os campos.');
}

if ($senha !== $confirmar) {
    exit('As senhas não coincidem.');
}

if (strlen($senha) < 6) {
    exit('A senha deve ter pelo menos 6 caracteres.');
}

$res = supabaseRequest(
    'GET',
    '/rest/v1/admins?email=eq.' . urlencode($email) . '&select=*',
    null,
    true
);

if (($res['status'] ?? 0) !== 200 || empty($res['body'])) {
    exit('Email não autorizado para admin.');
}

$admin = $res['body'][0];

if (!($admin['ativo'] ?? false)) {
    exit('Admin inativo.');
}

if (!empty($admin['senha_hash'])) {
    exit('Esse admin já possui senha cadastrada.');
}

$hash = password_hash($senha, PASSWORD_DEFAULT);

$update = supabaseRequest(
    'PATCH',
    '/rest/v1/admins?email=eq.' . urlencode($email),
    ['senha_hash' => $hash],
    true
);

if (($update['status'] ?? 0) >= 200 && ($update['status'] ?? 0) < 300) {
    header('Location: /admin-login.php');
    exit;
}

exit('Erro ao salvar senha.');