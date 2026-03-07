<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

require_once __DIR__ . '/supabase.php';

$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

if (!$email || !$senha) {
    exit('Email e senha são obrigatórios.');
}

$res = supabaseRequest(
    'GET',
    '/rest/v1/admins?email=eq.' . urlencode($email) . '&select=*',
    null,
    true
);

if (($res['status'] ?? 0) !== 200 || empty($res['body'])) {
    exit('Admin não encontrado.');
}

$admin = $res['body'][0];

if (!($admin['ativo'] ?? false)) {
    exit('Admin inativo.');
}

$senhaHash = $admin['senha_hash'] ?? '';

if (!$senhaHash || !password_verify($senha, $senhaHash)) {
    exit('Senha inválida.');
}

$_SESSION['admin_logado'] = true;
$_SESSION['admin_id'] = $admin['id'] ?? null;
$_SESSION['admin_email'] = $admin['email'] ?? '';
$_SESSION['admin_nome'] = $admin['nome'] ?? '';

header('Location: /admin.php');
exit;