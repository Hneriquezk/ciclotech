<?php
// auth.php
require_once __DIR__ . '/sessao.php';
function require_login() {
    if (empty($_SESSION['usuario']) || empty($_SESSION['usuario']['id'])) {
        header('Location: login.php');
        exit;
    }
}
