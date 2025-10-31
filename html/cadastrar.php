<?php
require_once __DIR__ . '/sessao.php';
require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/flash.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cadastro.php');
    exit;
}

// Captura os dados do formulário na ordem solicitada
$nome = trim($_POST['nome'] ?? '');
$sexo = trim($_POST['sexo'] ?? '');
$cpf = trim($_POST['cpf'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$nascimento = trim($_POST['nascimento'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';
$confirmar = $_POST['confirmar'] ?? '';

// Validações básicas
$errors = [];
if ($nome === '') $errors[] = 'Nome é obrigatório.';
if ($sexo === '') $errors[] = 'Sexo é obrigatório.';
if ($cpf === '' || !preg_match('/^\d{11}$/', preg_replace('/\D/', '', $cpf))) $errors[] = 'CPF inválido.';
if ($telefone === '') $errors[] = 'Telefone é obrigatório.';
if ($nascimento === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $nascimento)) $errors[] = 'Data de nascimento inválida. Use o formato YYYY-MM-DD.';
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'E-mail inválido.';
if (strlen($senha) < 6) $errors[] = 'Senha deve ter ao menos 6 caracteres.';
if ($senha !== $confirmar) $errors[] = 'Senhas não coincidem.';

if (!empty($errors)) {
    flash_set(implode(' ', $errors));
    header('Location: cadastro.php');
    exit;
}

// Usa a conexão já criada no conexao.php
$conn->set_charset("utf8mb4");

// Verifica se o e-mail já existe
$stmt = $conn->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    $conn->close();
    flash_set('Já existe uma conta com esse e-mail.');
    header('Location: cadastro.php');
    exit;
}
$stmt->close();

// Insere o novo usuário
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);
$ins = $conn->prepare('INSERT INTO usuarios (nome, sexo, cpf, telefone, nascimento, email, senha_hash) VALUES (?, ?, ?, ?, ?, ?, ?)');
$ins->bind_param('sssssss', $nome, $sexo, $cpf, $telefone, $nascimento, $email, $senha_hash);

if ($ins->execute()) {
    $ins->close();
    $conn->close();
    flash_set('Cadastro realizado com sucesso. Faça login.');
    header('Location: login.php');
    exit;
} else {
    $ins->close();
    $conn->close();
    flash_set('Erro ao salvar usuário. Tente novamente.');
    header('Location: cadastro.php');
    exit;
}
