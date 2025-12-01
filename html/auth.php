<?php
function require_login() {
    session_start();
    
    // Verifica se está logado como cliente normal
    $cliente_logado = isset($_SESSION['usuario_id']);
    
    // Se não estiver logado, redireciona para login
    if (!$cliente_logado) {
        header('Location: login.php');
        exit;
    }
}

// Função para verificar se está logado
function is_logged_in() {
    return isset($_SESSION['usuario_id']);
}

// Função para obter o ID do usuário logado
function get_user_id() {
    return $_SESSION['usuario_id'] ?? null;
}

// Função para obter o nome do usuário logado
function get_user_name() {
    return $_SESSION['usuario_nome'] ?? '';
}

// Função para obter o email do usuário logado
function get_user_email() {
    return $_SESSION['usuario_email'] ?? '';
}
?>