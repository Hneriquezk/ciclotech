<?php
require_once __DIR__ . '/sessao.php';
require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/flash.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

// Captura os dados do formulário
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

// Validação simples
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $senha === '') {
    flash_set('E-mail ou senha incorretos.');
    header('Location: login.php');
    exit;
}

// Usa a conexão já criada em conexao.php
$conn->set_charset("utf8mb4");

// Prepara a consulta
$stmt = $conn->prepare('SELECT id, nome, email, senha_hash FROM usuarios WHERE email = ? LIMIT 1');
if (!$stmt) {
    flash_set('Erro no servidor.');
    header('Location: login.php');
    exit;
}

$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows !== 1) {
    $stmt->close();
    $conn->close();
    flash_set('E-mail ou senha incorretos.');
    header('Location: login.php');
    exit;
}

// Recupera os dados do usuário
$stmt->bind_result($id, $nome, $email_db, $senha_hash);
$stmt->fetch();

// Verifica a senha
if (!password_verify($senha, $senha_hash)) {
    $stmt->close();
    $conn->close();
    flash_set('E-mail ou senha incorretos.');
    header('Location: login.php');
    exit;
}

// Inicia a sessão
session_regenerate_id(true);
$_SESSION['usuario'] = [
    'id' => $id,
    'nome' => $nome,
    'email' => $email_db
];

$stmt->close();
$conn->close();

// Redireciona para home
header('Location: home.php');
exit;
