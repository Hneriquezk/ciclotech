<?php
session_start();
if (!isset($_SESSION['logado'])) {
    header("Location: login.php");
    exit;
}

include('config.php');

// Criar pasta de upload se não existir
if (!is_dir('uploads_produtos')) {
    mkdir('uploads_produtos', 0777, true);
}

// Adicionar produto
if (isset($_POST['adicionar'])) {
    $nome = $conn->real_escape_string($_POST['nome']);
    $preco = floatval($_POST['preco']);
    $estoque = intval($_POST['estoque']);
    $descricao = $conn->real_escape_string($_POST['descricao'] ?? '');
    $desconto = floatval($_POST['desconto'] ?? 0);
    $codigo_tipo = $conn->real_escape_string($_POST['codigo_tipo']);

    // Upload de imagem
    $imagem = null;
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
        $nome_arquivo = time() . '_' . basename($_FILES['imagem']['name']);
        $caminho = 'uploads_produtos/' . $nome_arquivo;
        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho)) {
            $imagem = $caminho;
        }
    }

    // Usando prepared statement para segurança
    $sql = "INSERT INTO produtos (nome, preco, estoque, imagem, descricao, desconto, codigo_tipo) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdissss", $nome, $preco, $estoque, $imagem, $descricao, $desconto, $codigo_tipo);
    
    if ($stmt->execute()) {
        header("Location: produtos.php");
        exit;
    } else {
        $erro = "Erro ao adicionar produto: " . $conn->error;
    }
}

// Excluir produto
if (isset($_GET['excluir'])) {
    $id = intval($_GET['excluir']);
    
    // Primeiro busca a imagem para excluir do servidor
    $result = $conn->query("SELECT imagem FROM produtos WHERE id = $id");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['imagem'] && file_exists($row['imagem'])) {
            unlink($row['imagem']);
        }
    }
    
    $conn->query("DELETE FROM produtos WHERE id = $id");
    header("Location: produtos.php");
    exit;
}

// Atualizar produto
if (isset($_POST['editar'])) {
    $id = intval($_POST['id']);
    $nome = $conn->real_escape_string($_POST['nome']);
    $preco = floatval($_POST['preco']);
    $estoque = intval($_POST['estoque']);
    $descricao = $conn->real_escape_string($_POST['descricao'] ?? '');
    $desconto = floatval($_POST['desconto'] ?? 0);
    $codigo_tipo = $conn->real_escape_string($_POST['codigo_tipo']);

    // Se houver nova imagem, substitui
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
        // Remove imagem antiga se existir
        $result = $conn->query("SELECT imagem FROM produtos WHERE id = $id");
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['imagem'] && file_exists($row['imagem'])) {
                unlink($row['imagem']);
            }
        }
        
        $nome_arquivo = time() . '_' . basename($_FILES['imagem']['name']);
        $caminho = 'uploads_produtos/' . $nome_arquivo;
        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho)) {
            $conn->query("UPDATE produtos SET imagem='$caminho' WHERE id=$id");
        }
    }

    // Usando prepared statement para segurança
    $sql = "UPDATE produtos SET nome=?, preco=?, estoque=?, descricao=?, desconto=?, codigo_tipo=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdisssi", $nome, $preco, $estoque, $descricao, $desconto, $codigo_tipo, $id);
    
    if ($stmt->execute()) {
        header("Location: produtos.php");
        exit;
    } else {
        $erro = "Erro ao atualizar produto: " . $conn->error;
    }
}

// Filtro por código
$filtro_codigo = isset($_GET['filtro']) ? $_GET['filtro'] : '';
$where = "";
if ($filtro_codigo && $filtro_codigo !== 'TODOS') {
    $filtro_codigo = $conn->real_escape_string($filtro_codigo);
    $where = "WHERE codigo_tipo = '$filtro_codigo'";
}

// Buscar produtos
$result = $conn->query("SELECT * FROM produtos $where ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Produtos - Ciclotech</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet">
</head>
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', sans-serif;
    background-color: #0d0d0d;
    color: #f5f5f5;
    margin: 0;
    padding: 0;
    overflow-x: hidden;
}

/* ======= HEADER COM IMAGEM DE FUNDO ======= */
.header {
    background: 
    background-size: cover;
    background-position: center;
    height: 200px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 20px 30px;
    position: relative;
    border-bottom: 3px solid #d00000;
}

/* Menu toggle integrado na imagem */
.menu-toggle {
    position: absolute;
    top: 20px;
    left: 30px;
    background: rgba(208, 0, 0, 0.9);
    color: #fff;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
    z-index: 101;
    border: 2px solid #fff;
}

.menu-toggle:hover {
    background: rgba(255, 255, 255, 0.9);
    color: #d00000;
    transform: scale(1.1);
}

.menu-toggle i {
    font-size: 24px;
}

.logo {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

.logo img {
    height: 80px;
    filter: drop-shadow(0 2px 10px rgba(0, 0, 0, 0.7));
}

.logo-text {
    color: #fff;
    font-size: 24px;
    font-weight: bold;
    margin-top: 10px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
    font-family: 'Anton', sans-serif;
    letter-spacing: 2px;
}

.header-right {
    position: absolute;
    top: 20px;
    right: 30px;
}

.sair {
    color: #fff;
    background-color: rgba(208, 0, 0, 0.9);
    padding: 10px 20px;
    border-radius: 25px;
    text-decoration: none;
    transition: all 0.3s ease;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.sair:hover {
    background-color: rgba(255, 255, 255, 0.9);
    color: #d00000;
    transform: translateY(-2px);
}

/* ======= SIDEBAR ======= */
.sidebar {
    width: 280px;
    height: calc(100vh - 200px);
    background: 
        linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
    position: fixed;
    top: 200px;
    left: -280px;
    transition: all 0.3s ease;
    z-index: 99;
    overflow-y: auto;
    padding: 20px 0;
    border-right: 2px solid #d00000;
    box-shadow: 5px 0 15px rgba(0, 0, 0, 0.5);
}

.sidebar.active {
    left: 0;
}

.user-card {
    background: 
        linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
    color: #fff;
    padding: 25px;
    border-radius: 15px;
    margin: 0 15px 20px;
    text-align: center;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    border: 1px solid #333;
}

.user-avatar {
    margin-bottom: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.avatar-iniciais {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, #d00000 0%, #b30000 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    border: 4px solid #dc2626;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    color: white;
    font-size: 32px;
    font-weight: bold;
    font-family: 'Poppins', sans-serif;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    transition: all 0.3s ease;
}

.avatar-iniciais:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(208, 0, 0, 0.4);
}

.user-info {
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 2px solid #333;
}

.user-name {
    margin: 0 0 5px 0;
    font-size: 22px;
    font-weight: 700;
    color: #ffffff;
}

.user-role {
    margin: 0;
    font-size: 14px;
    color: #ccc;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.user-links {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.nav-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 20px;
    background-color: #1a1a1a;
    color: #fff;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.nav-link:hover {
    background-color: #333;
    color: white;
    transform: translateX(5px);
}

.nav-link.active {
    background-color: #dc2626;
    color: white;
    border-color: #b91c1c;
}

.nav-link i {
    width: 20px;
    text-align: center;
}

/* ======= CONTEÚDO PRINCIPAL ======= */
.content-area {
    padding: 30px 40px;
    transition: all 0.3s ease;
    min-height: calc(100vh - 200px);
}

.content-area.expanded {
    margin-left: 280px;
}

/* ======= ESTILOS DOS PRODUTOS ======= */
.titulo-produtos {
    text-align: center;
    color: #fff;
    font-size: 28px;
    margin-bottom: 30px;
    text-transform: uppercase;
    letter-spacing: 1px;
    position: relative;
    padding-bottom: 10px;
}

.titulo-produtos::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 3px;
    background-color: #d00000;
}

/* Filtro */
.filtro-container {
    background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 30px;
    border: 1px solid #333;
}

.filtro-form {
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}

.filtro-label {
    color: #fff;
    font-weight: 600;
    font-size: 14px;
}

.filtro-select {
    background: #333;
    color: #fff;
    border: 1px solid #555;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 14px;
}

.btn-limpar-filtro {
    background: #666;
    color: white;
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.3s ease;
}

.btn-limpar-filtro:hover {
    background: #777;
}

.contador-resultados {
    margin-top: 10px;
    color: #ccc;
    font-size: 14px;
}

/* Tabela de produtos */
.tabela-produtos {
    width: 100%;
    border-collapse: collapse;
    background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #333;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    margin-bottom: 40px;
}

.tabela-produtos th {
    background: linear-gradient(135deg, #d00000 0%, #b30000 100%);
    color: #fff;
    padding: 15px 10px;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
    font-size: 14px;
    text-align: center;
}

.tabela-produtos td {
    padding: 15px 10px;
    border-bottom: 1px solid #333;
    vertical-align: middle;
    text-align: center;
}

.tabela-produtos tr:hover {
    background-color: #222;
}

.tabela-produtos tr:last-child td {
    border-bottom: none;
}

/* IMAGENS PADRONIZADAS */
.img-produto {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid #444;
    background: #333;
    display: block;
    margin: 0 auto;
}

/* Descrição */
.descricao-coluna {
    max-width: 200px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Códigos */
.codigo-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.codigo-bike { background: #4CAF50; color: white; }
.codigo-pneu { background: #2196F3; color: white; }
.codigo-traje { background: #FF9800; color: white; }
.codigo-equipamento { background: #9C27B0; color: white; }

/* Preços */
.preco-container {
    display: flex;
    flex-direction: column;
    gap: 2px;
    align-items: center;
}

.preco-original {
    text-decoration: line-through;
    color: #999;
    font-size: 12px;
}

.preco-com-desconto {
    color: #4CAF50;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
}

.desconto-badge {
    background: #4CAF50;
    color: white;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 10px;
}

/* Estoque */
.estoque-normal {
    color: #4CAF50;
    font-weight: 600;
}

.estoque-baixo {
    color: #f44336;
    font-weight: 600;
    background: rgba(244, 67, 54, 0.1);
    padding: 4px 8px;
    border-radius: 4px;
}

/* Botões */
.btn-editar {
    background: #4CAF50;
    color: white;
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
    margin-right: 5px;
    display: inline-block;
    border: none;
    cursor: pointer;
}

.btn-editar:hover {
    background: #45a049;
    transform: translateY(-2px);
}

.btn-excluir {
    background: #dc2626;
    color: white;
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
    display: inline-block;
    border: none;
    cursor: pointer;
}

.btn-excluir:hover {
    background: #b91c1c;
    transform: translateY(-2px);
}

.btn-adicionar-grande {
    background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
    color: white;
    padding: 15px 30px;
    border-radius: 10px;
    border: none;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0 auto;
}

.btn-adicionar-grande:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(76, 175, 80, 0.3);
}

.icone-adicionar {
    font-size: 20px;
    font-weight: bold;
}

.botoes-acao-container {
    text-align: center;
    margin-top: 30px;
}

/* Modais */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    z-index: 1000;
    align-items: center;
    justify-content: center;
}

.modal-conteudo {
    background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
    padding: 30px;
    border-radius: 15px;
    width: 90%;
    max-width: 500px;
    border: 2px solid #d00000;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
}

.modal-conteudo h3 {
    color: #fff;
    margin-bottom: 20px;
    text-align: center;
    font-size: 24px;
}

.modal-conteudo input,
.modal-conteudo textarea,
.modal-conteudo select {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    background: #333;
    border: 1px solid #555;
    border-radius: 6px;
    color: #fff;
    font-size: 14px;
}

.modal-conteudo input:focus,
.modal-conteudo textarea:focus,
.modal-conteudo select:focus {
    outline: none;
    border-color: #d00000;
}

.file-input-container {
    position: relative;
    margin-bottom: 15px;
}

.file-input-container input[type="file"] {
    display: none;
}

.file-input-label {
    display: block;
    padding: 12px;
    background: #444;
    color: #fff;
    border: 1px dashed #666;
    border-radius: 6px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.file-input-label:hover {
    background: #555;
    border-color: #d00000;
}

.modal-botoes {
    display: flex;
    gap: 10px;
    justify-content: center;
}

.modal-botoes button {
    padding: 12px 24px;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.modal-botoes button[type="submit"] {
    background: #4CAF50;
    color: white;
}

.modal-botoes button[type="submit"]:hover {
    background: #45a049;
}

.btn-cancelar {
    background: #666;
    color: white;
}

.btn-cancelar:hover {
    background: #777;
}

/* Mensagens de erro */
.erro-mensagem {
    background: #f44336;
    color: white;
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 20px;
    text-align: center;
}

.sem-resultados {
    text-align: center;
    color: #999;
    padding: 40px;
    font-style: italic;
}

footer {
  margin-top: 40px;
}

.footer-top {
  background: #d80000;
  color: #fff;
  display: flex;
  justify-content: space-around;
  align-items: center;
  padding: 10px 0;
  font-weight: bold;
  text-align: center;
}

.footer-main {
  background: #000;
  color: #fff;
  display: grid;
  grid-template-columns: 1fr 1fr 1fr 1fr;
  gap: 30px;
  padding: 40px 80px;
  align-items: flex-start;
}

.footer-logo img {
  max-width: 180px;
  margin-bottom: 15px;
}

.footer-logo p {
  font-size: 14px;
  color: #ddd;
}

.footer-section h3 {
  margin-bottom: 15px;
  font-size: 18px;
}

.footer-section ul {
  list-style: none;
}

.footer-section ul li {
  margin-bottom: 10px;
}

.footer-section ul li a {
  text-decoration: none;
  color: #ddd;
  font-size: 14px;
}

.footer-section ul li a:hover {
  color: #fff;
}

.footer-contact p {
  margin-bottom: 8px;
  font-size: 14px;
}

.footer-social {
  display: flex;
  gap: 15px;
  margin-top: 10px;
}

.footer-social img {
  width: 28px;
  height: 28px;
  cursor: pointer;
}

.footer-payment img {
  width: 40px;
  margin: 5px;
}

.footer-payment {
  text-align: center;
}

.footer-section footer-contact {
  color: white;
}

/* Reset mais agressivo para o footer */
footer .footer-main,
footer .footer-main * {
    color: white !important;
}

footer .footer-logo p,
footer .footer-section ul li a,
footer .footer-contact p {
    color: #ddd !important;
}

/* Manter os links com cor mais clara no hover */
footer .footer-section ul li a:hover {
    color: white !important;
}

/* ======= RESPONSIVIDADE ======= */
@media (max-width: 768px) {
    .header {
        height: 180px;
        padding: 15px 20px;
    }
    
    .menu-toggle {
        top: 15px;
        left: 20px;
        width: 45px;
        height: 45px;
    }
    
    .logo img {
        height: 60px;
    }
    
    .logo-text {
        font-size: 20px;
    }
    
    .sair {
        padding: 8px 16px;
        font-size: 14px;
    }
    
    .content-area {
        padding: 20px;
    }
    
    .tabela-produtos {
        font-size: 14px;
    }
    
    .img-produto {
        width: 60px;
        height: 60px;
    }
}

@media (max-width: 480px) {
    .header {
        height: 160px;
        padding: 10px 15px;
    }
    
    .menu-toggle {
        width: 40px;
        height: 40px;
    }
    
    .menu-toggle i {
        font-size: 20px;
    }
    
    .logo img {
        height: 50px;
    }
    
    .logo-text {
        font-size: 18px;
    }
    
    .sair {
        padding: 8px 16px;
        font-size: 14px;
    }
    
    .content-area {
        padding: 15px;
    }
    
    .tabela-produtos th,
    .tabela-produtos td {
        padding: 12px 8px;
        font-size: 13px;
    }
    
    .img-produto {
        width: 50px;
        height: 50px;
    }
    
    .filtro-form {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
<body>
    <!-- Header com imagem de fundo e menu integrado -->
    <div class="header">
        <div class="menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
        
        <div class="logo">
            <img src="../img/logo.png" alt="Ciclotech">
        </div>
        
        <div class="header-right">
            <a href="logout.php" class="sair">
                <i class="fas fa-sign-out-alt"></i> Sair
            </a>
        </div>
    </div>
    
    <!-- Sidebar recolhível -->
    <div class="sidebar">
        <div class="user-card">
            <div class="user-avatar">
                <div class="avatar-iniciais" data-iniciais="AD">
                    AD
                </div>
            </div>
            <div class="user-info">
                <h2 class="user-name">ADMINISTRADOR</h2>
                <p class="user-role">Administrador do Sistema</p>
            </div>
            
            <div class="user-links">
                <a href="dashboard.php" class="nav-link">
                    <i class="fas fa-home"></i> Início
                </a>
                <a href="pedidos.php" class="nav-link">
                    <i class="fas fa-shopping-cart"></i> Pedidos
                </a>
                <a href="clientes.php" class="nav-link">
                    <i class="fas fa-users"></i> Clientes
                </a>
                <a href="produtos.php" class="nav-link active">
                    <i class="fas fa-box"></i> Produtos
                </a>
                <a href="relatorios.php" class="nav-link">
                    <i class="fas fa-chart-bar"></i> Relatórios
                </a>
            </div>
        </div>
    </div>

    <!-- Conteúdo principal -->
    <div class="content-area">
        <h2 class="titulo-produtos">GERENCIAR PRODUTOS</h2>

        <?php if (isset($erro)): ?>
            <div class="erro-mensagem"><?= $erro ?></div>
        <?php endif; ?>

        <!-- Filtro por Código -->
        <div class="filtro-container">
            <form method="GET" class="filtro-form">
                <div class="filtro-group">
                    <label for="filtro-codigo" class="filtro-label">FILTRAR POR CÓDIGO:</label>
                    <select name="filtro" id="filtro-codigo" class="filtro-select" onchange="this.form.submit()">
                        <option value="TODOS" <?= $filtro_codigo === 'TODOS' || $filtro_codigo === '' ? 'selected' : '' ?>>TODOS OS PRODUTOS</option>
                        <option value="BIKE" <?= $filtro_codigo === 'BIKE' ? 'selected' : '' ?>>BIKE - BICICLETAS</option>
                        <option value="PN" <?= $filtro_codigo === 'PN' ? 'selected' : '' ?>>PN - PNEUS</option>
                        <option value="TR" <?= $filtro_codigo === 'TR' ? 'selected' : '' ?>>TR - TRAJES</option>
                        <option value="EQ" <?= $filtro_codigo === 'EQ' ? 'selected' : '' ?>>EQ - EQUIPAMENTOS</option>
                    </select>
                    <?php if ($filtro_codigo): ?>
                        <a href="produtos.php" class="btn-limpar-filtro">Limpar Filtro</a>
                    <?php endif; ?>
                </div>
            </form>
            
            <!-- Contador de resultados -->
            <div class="contador-resultados">
                <?php
                $total_produtos = $result->num_rows;
                if ($filtro_codigo && $filtro_codigo !== 'TODOS') {
                    $codigo_texto = '';
                    switch($filtro_codigo) {
                        case 'BIKE': $codigo_texto = 'BICICLETAS'; break;
                        case 'PN': $codigo_texto = 'PNEUS'; break;
                        case 'TR': $codigo_texto = 'TRAJES'; break;
                        case 'EQ': $codigo_texto = 'EQUIPAMENTOS'; break;
                        default: $codigo_texto = $filtro_codigo;
                    }
                    echo "<span class='resultado-filtro'>Mostrando $total_produtos produto(s) - $codigo_texto</span>";
                } else {
                    echo "<span class='resultado-total'>Total: $total_produtos produto(s)</span>";
                }
                ?>
            </div>
        </div>

        <!-- Tabela -->
        <table class="tabela-produtos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Imagem</th>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Código</th>
                    <th>Preço</th>
                    <th>Estoque</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): 
                        $precoComDesconto = $row['preco'];
                        if ($row['desconto'] > 0) {
                            $precoComDesconto = $row['preco'] * (1 - $row['desconto'] / 100);
                        }
                        
                        // Definir classe do estoque
                        $estoqueClass = ($row['estoque'] <= 45) ? 'estoque-baixo' : 'estoque-normal';
                        
                        // Definir texto do código
                        $codigoText = '';
                        $codigoClass = '';
                        switch($row['codigo_tipo']) {
                            case 'BIKE':
                                $codigoText = 'Bike';
                                $codigoClass = 'codigo-bike';
                                break;
                            case 'PN':
                                $codigoText = 'Pneu';
                                $codigoClass = 'codigo-pneu';
                                break;
                            case 'TR':
                                $codigoText = 'Traje';
                                $codigoClass = 'codigo-traje';
                                break;
                            case 'EQ':
                                $codigoText = 'Equipamento';
                                $codigoClass = 'codigo-equipamento';
                                break;
                            default:
                                $codigoText = $row['codigo_tipo'];
                                $codigoClass = 'codigo-bike';
                        }
                    ?>
                        <tr>
                            <td>#<?= $row['id'] ?></td>
                            <td>
                                <?php if ($row['imagem']): ?>
                                    <img src="<?= $row['imagem'] ?>" alt="produto" class="img-produto">
                                <?php else: ?>
                                    <div class="img-produto" style="background: #333; display: flex; align-items: center; justify-content: center; color: #666; font-size: 12px;">
                                        Sem imagem
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['nome']) ?></td>
                            <td class="descricao-coluna" title="<?= htmlspecialchars($row['descricao']) ?>">
                                <?= htmlspecialchars($row['descricao']) ?>
                            </td>
                            <td>
                                <span class="codigo-badge <?= $codigoClass ?>">
                                    <?= $codigoText ?>
                                </span>
                            </td>
                            <td>
                                <div class="preco-container">
                                    <?php if ($row['desconto'] > 0): ?>
                                        <span class="preco-original">R$ <?= number_format($row['preco'], 2, ',', '.') ?></span>
                                        <span class="preco-com-desconto">
                                            R$ <?= number_format($precoComDesconto, 2, ',', '.') ?>
                                            <span class="desconto-badge">-<?= $row['desconto'] ?>%</span>
                                        </span>
                                    <?php else: ?>
                                        R$ <?= number_format($row['preco'], 2, ',', '.') ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="<?= $estoqueClass ?>" title="<?= ($row['estoque'] <= 45) ? 'Estoque baixo!' : 'Estoque normal' ?>">
                                <?= $row['estoque'] ?>
                            </td>
                            <td>
                                <a href="?excluir=<?= $row['id'] ?>" class="btn-excluir" onclick="return confirm('Excluir este produto?')">Excluir</a>
                                <button class="btn-editar" onclick="abrirModalEditar(<?= $row['id'] ?>, '<?= addslashes($row['nome']) ?>', <?= $row['preco'] ?>, <?= $row['estoque'] ?>, '<?= addslashes($row['descricao']) ?>', <?= $row['desconto'] ?>, '<?= $row['codigo_tipo'] ?>')">Editar</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="sem-resultados">
                            <?php if ($filtro_codigo && $filtro_codigo !== 'TODOS'): ?>
                                Nenhum produto encontrado para o filtro selecionado.
                            <?php else: ?>
                                Nenhum produto cadastrado.
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Espaçamento extra -->
        <div style="height: 60px;"></div>

        <!-- Botão para adicionar produto -->
        <div class="botoes-acao-container">
            <button class="btn-adicionar-grande" onclick="abrirModalAdicionar()">
                <i class="icone-adicionar">+</i>
                Adicionar Novo Produto
            </button>
        </div>
    </div>

    <!-- Modal para adicionar produto -->
    <div id="modal-adicionar" class="modal">
        <div class="modal-conteudo">
            <h3>Adicionar Novo Produto</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="text" name="nome" placeholder="Nome do produto" required>
                
                <div class="form-group">
                    <label for="codigo_tipo">Código do Produto:</label>
                    <select name="codigo_tipo" id="codigo_tipo" required>
                        <option value="">Selecione o código</option>
                        <option value="BIKE">BIKE - Bicicletas</option>
                        <option value="PN">PN - Pneus</option>
                        <option value="TR">TR - Trajes</option>
                        <option value="EQ">EQ - Equipamentos</option>
                    </select>
                </div>
                
                <input type="number" step="0.01" name="preco" placeholder="Preço (R$)" required>
                <input type="number" name="estoque" placeholder="Estoque" required>
                <textarea name="descricao" placeholder="Descrição do produto"></textarea>
                <input type="number" step="0.01" name="desconto" placeholder="Desconto (%)" min="0" max="100">
                
                <div class="file-input-container">
                    <input type="file" name="imagem" id="imagem" accept="image/*">
                    <label for="imagem" class="file-input-label">Escolher imagem</label>
                </div>
                
                <div class="modal-botoes">
                    <button type="submit" name="adicionar">Adicionar Produto</button>
                    <button type="button" class="btn-cancelar" onclick="fecharModalAdicionar()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de edição -->
    <div id="modal-editar" class="modal">
        <div class="modal-conteudo">
            <h3>Editar Produto</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="edit-id">
                <input type="text" name="nome" id="edit-nome" placeholder="Nome do produto" required>
                
                <div class="form-group">
                    <label for="edit-codigo_tipo">Código do Produto:</label>
                    <select name="codigo_tipo" id="edit-codigo_tipo" required>
                        <option value="">Selecione o código</option>
                        <option value="BIKE">BIKE - Bicicletas</option>
                        <option value="PN">PN - Pneus</option>
                        <option value="TR">TR - Trajes</option>
                        <option value="EQ">EQ - Equipamentos</option>
                    </select>
                </div>
                
                <input type="number" step="0.01" name="preco" id="edit-preco" placeholder="Preço (R$)" required>
                <input type="number" name="estoque" id="edit-estoque" placeholder="Estoque" required>
                <textarea name="descricao" id="edit-descricao" placeholder="Descrição do produto"></textarea>
                <input type="number" step="0.01" name="desconto" id="edit-desconto" placeholder="Desconto (%)" min="0" max="100">
                
                <div class="file-input-container">
                    <input type="file" name="imagem" id="edit-imagem" accept="image/*">
                    <label for="edit-imagem" class="file-input-label">Escolher nova imagem (opcional)</label>
                </div>
                
                <div class="modal-botoes">
                    <button type="submit" name="editar">Salvar Alterações</button>
                    <button type="button" class="btn-cancelar" onclick="fecharModalEditar()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
    <footer>
        <div class="footer-top">
            <span>Entregamos para o Brasil inteiro</span>
            <span>Desconto no pix: 10%</span>
            <span>SITE 100% SEGURO</span>
        </div>
        <div class="footer-main">
            <div class="footer-logo">
                <img src="../img/logo.png" alt="Ciclotech">
                <p>A loja mais especializada em itens de ciclismo geral no Brasil.</p>
            </div>
            <div class="footer-section">
                <h3>Informações</h3>
                <ul>
                    <li><a href="home.php">Ciclotech</a></li>
                    <li><a href="faq.php">Política De Privacidade</a></li>
                    <li><a href="faq.php">Trocas e Devoluções</a></li>
                </ul>
            </div>
            <div class="footer-section footer-contact">
                <h3>Atendimento</h3>
                <p>Segunda a Sexta<br>10:00 ás 16:00</p>
                <br>
                <h3>Contato</h3>
                <p>(12)99729-1076</p>
                <h3>Redes Sociais</h3>
                <div class="footer-social">
                    <a href="https://www.instagram.com/ciclotechog/"><img src="../img/instagram.png" alt="Instagram"></a>
                    <a href="https://www.facebook.com/people/Ciclotech-CicloDevs/pfbid0fiNkHDuAtKPdAhwkQqdgLk4YYRBgQean95dUFzEVjg1eQH4CHDMdH5dUHTUaALrAl/"><img src="../img/facebook.png" alt="Facebook"></a>
                </div>
            </div>
            <div class="footer-section footer-payment">
                <h3>Formas de Pagamento</h3>
                <div>
                    <img src="../img/visa.jpg" alt="Visa">
                    <img src="../img/mastercard.png" alt="Mastercard">
                    <img src="../img/hipercard.png" alt="Hipercard">
                    <img src="../img/elo.png" alt="Elo">
                    <img src="../img/boleto.png" alt="Boleto">
                    <img src="../img/pix.png" alt="Pix">
                    <img src="../img/paypal.png" alt="Paypal">
                </div>
            </div>
        </div>
    </footer>
    <script>
    // Controle do menu lateral
    document.addEventListener('DOMContentLoaded', function() {
        const menuToggle = document.querySelector('.menu-toggle');
        const sidebar = document.querySelector('.sidebar');
        const contentArea = document.querySelector('.content-area');
        
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            
            // Ajustar margem do conteúdo principal
            if (contentArea) {
                contentArea.classList.toggle('expanded');
            }
            
            // Alterar ícone do menu
            const icon = this.querySelector('i');
            if (sidebar.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
        
        // Fechar menu ao clicar em um link
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function() {
                sidebar.classList.remove('active');
                
                if (contentArea) {
                    contentArea.classList.remove('expanded');
                }
                
                const menuIcon = menuToggle.querySelector('i');
                menuIcon.classList.remove('fa-times');
                menuIcon.classList.add('fa-bars');
                
                // Remover classe active de todos os links
                document.querySelectorAll('.nav-link').forEach(nav => {
                    nav.classList.remove('active');
                });
                
                // Adicionar classe active ao link clicado
                this.classList.add('active');
            });
        });
        
        // Fechar menu ao clicar fora dele
        document.addEventListener('click', function(event) {
            const isClickInsideSidebar = sidebar.contains(event.target);
            const isClickInsideMenuToggle = menuToggle.contains(event.target);
            
            if (!isClickInsideSidebar && !isClickInsideMenuToggle && sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
                
                if (contentArea) {
                    contentArea.classList.remove('expanded');
                }
                
                const menuIcon = menuToggle.querySelector('i');
                menuIcon.classList.remove('fa-times');
                menuIcon.classList.add('fa-bars');
            }
        });
        
        // Fechar menu com tecla ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
                
                if (contentArea) {
                    contentArea.classList.remove('expanded');
                }
                
                const menuIcon = menuToggle.querySelector('i');
                menuIcon.classList.remove('fa-times');
                menuIcon.classList.add('fa-bars');
            }
        });
    });

    // Funções dos modais
    function abrirModalAdicionar() {
        document.getElementById('modal-adicionar').style.display = 'flex';
    }

    function fecharModalAdicionar() {
        document.getElementById('modal-adicionar').style.display = 'none';
    }

    function abrirModalEditar(id, nome, preco, estoque, descricao, desconto, codigoTipo) {
        document.getElementById('modal-editar').style.display = 'flex';
        document.getElementById('edit-id').value = id;
        document.getElementById('edit-nome').value = nome;
        document.getElementById('edit-preco').value = preco;
        document.getElementById('edit-estoque').value = estoque;
        document.getElementById('edit-descricao').value = descricao || '';
        document.getElementById('edit-desconto').value = desconto || 0;
        document.getElementById('edit-codigo_tipo').value = codigoTipo || '';
    }

    function fecharModalEditar() {
        document.getElementById('modal-editar').style.display = 'none';
    }

    // Fechar modais ao clicar fora
    document.querySelectorAll('.modal').forEach(function(modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                if (this.id === 'modal-adicionar') {
                    fecharModalAdicionar();
                } else {
                    fecharModalEditar();
                }
            }
        });
    });

    // Atualizar o texto do label do file input quando um arquivo for selecionado
    document.querySelectorAll('input[type="file"]').forEach(function(input) {
        input.addEventListener('change', function(e) {
            const label = this.nextElementSibling;
            if (this.files && this.files[0]) {
                label.textContent = this.files[0].name;
            } else {
                label.textContent = this.id === 'edit-imagem' ? 'Escolher nova imagem (opcional)' : 'Escolher imagem';
            }
        });
    });

    // Fechar modais com ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            fecharModalAdicionar();
            fecharModalEditar();
        }
    });
    </script>

</body>
</html>