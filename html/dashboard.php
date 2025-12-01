<?php
session_start();
if (!isset($_SESSION['logado'])) {
    header("Location: login.php");
    exit;
}

include('config.php');


// ========== DADOS REAIS PARA OS CARDS ==========

// 1. RECEITA TOTAL (MÊS) - Buscar da tabela entregues dos últimos 30 dias
$receitaMesQuery = $conn->query("
    SELECT SUM(valor) as total_receita 
    FROM entregues 
    WHERE hora_entrega >= DATE_SUB(NOW(), INTERVAL 30 DAY)
");
$receitaMes = $receitaMesQuery->fetch_assoc();
$receita = $receitaMes['total_receita'] ? floatval($receitaMes['total_receita']) : 0.00;

// 2. PEDIDOS NOVOS - Contar pedidos pendentes
$pedidosPendentesQuery = $conn->query("SELECT COUNT(*) as total_pedidos FROM pedidos");
$pedidosPendentes = $pedidosPendentesQuery->fetch_assoc();
$pedidos = $pedidosPendentes['total_pedidos'] ? intval($pedidosPendentes['total_pedidos']) : 0;

// 3. NOVOS CLIENTES - Contar clientes cadastrados nos últimos 7 dias
$novosClientesQuery = $conn->query("
    SELECT COUNT(*) as total_clientes 
    FROM clientes 
    WHERE data_cadastro >= DATE_SUB(NOW(), INTERVAL 7 DAY)
");
$novosClientes = $novosClientesQuery->fetch_assoc();
$clientes = $novosClientes['total_clientes'] ? intval($novosClientes['total_clientes']) : 0;

// 4. PRODUTOS ESGOTADOS - Contar produtos com estoque baixo (≤ 45)
$produtosEsgotadosQuery = $conn->query("
    SELECT COUNT(*) as total_esgotados 
    FROM produtos 
    WHERE estoque <= 45
");
$produtosEsgotados = $produtosEsgotadosQuery->fetch_assoc();
$produtosEsgotados = $produtosEsgotados['total_esgotados'] ? intval($produtosEsgotados['total_esgotados']) : 0;

// 5. Calcular variação da receita em relação ao mês anterior
$receitaMesAnteriorQuery = $conn->query("
    SELECT SUM(valor) as total_receita 
    FROM entregues 
    WHERE hora_entrega BETWEEN DATE_SUB(NOW(), INTERVAL 60 DAY) AND DATE_SUB(NOW(), INTERVAL 30 DAY)
");
$receitaMesAnterior = $receitaMesAnteriorQuery->fetch_assoc();
$receitaAnterior = $receitaMesAnterior['total_receita'] ? floatval($receitaMesAnterior['total_receita']) : 1;

$variacaoReceita = $receitaAnterior > 0 ? (($receita - $receitaAnterior) / $receitaAnterior) * 100 : 0;

// 6. Calcular variação de pedidos (hoje vs ontem)
$pedidosHojeQuery = $conn->query("
    SELECT COUNT(*) as total_hoje 
    FROM pedidos 
    WHERE DATE(data_pedido) = CURDATE()
");
$pedidosHoje = $pedidosHojeQuery->fetch_assoc();
$totalHoje = $pedidosHoje['total_hoje'] ? intval($pedidosHoje['total_hoje']) : 0;

$pedidosOntemQuery = $conn->query("
    SELECT COUNT(*) as total_ontem 
    FROM pedidos 
    WHERE DATE(data_pedido) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
");
$pedidosOntem = $pedidosOntemQuery->fetch_assoc();
$totalOntem = $pedidosOntem['total_ontem'] ? intval($pedidosOntem['total_ontem']) : 0;

$diferencaPedidos = $totalHoje - $totalOntem;

// 7. Calcular variação de novos clientes (últimos 7 dias vs 7 dias anteriores)
$clientesSemanaAnteriorQuery = $conn->query("
    SELECT COUNT(*) as total_clientes 
    FROM clientes 
    WHERE data_cadastro BETWEEN DATE_SUB(NOW(), INTERVAL 14 DAY) AND DATE_SUB(NOW(), INTERVAL 7 DAY)
");
$clientesSemanaAnterior = $clientesSemanaAnteriorQuery->fetch_assoc();
$clientesAnterior = $clientesSemanaAnterior['total_clientes'] ? intval($clientesSemanaAnterior['total_clientes']) : 1;

$variacaoClientes = $clientesAnterior > 0 ? (($clientes - $clientesAnterior) / $clientesAnterior) * 100 : 0;

// 8. Calcular variação de produtos esgotados (hoje vs ontem)
$produtosEsgotadosOntemQuery = $conn->query("
    SELECT COUNT(*) as total_ontem 
    FROM produtos 
    WHERE estoque <= 45
    AND DATE(data_atualizacao) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
");
$produtosEsgotadosOntem = $produtosEsgotadosOntemQuery->fetch_assoc();
$totalEsgotadosOntem = $produtosEsgotadosOntem['total_ontem'] ? intval($produtosEsgotadosOntem['total_ontem']) : 0;

$diferencaEsgotados = $produtosEsgotados - $totalEsgotadosOntem;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Admin - Ciclotech</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

/* ======= CORREÇÕES PARA O AVATAR ======= */
.user-avatar {
    margin-bottom: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.user-avatar img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #dc2626;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    background: linear-gradient(45deg, #333, #555); /* Fallback background */
}

/* Fallback caso a imagem não carregue */
.user-avatar::before {
    content: '👤';
    font-size: 40px;
    position: absolute;
    opacity: 0;
}

.user-avatar img:not([src]), 
.user-avatar img[src="user.png"],
.user-avatar img[src*="undefined"] {
    background: linear-gradient(45deg, #d00000, #ff6b6b);
    display: flex;
    align-items: center;
    justify-content: center;
}

.user-avatar img:not([src])::after, 
.user-avatar img[src=""]::after,
.user-avatar img[src*="undefined"]::after {
    content: 'ADM';
    color: white;
    font-weight: bold;
    font-size: 18px;
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

/* ======= DASHBOARD ======= */
.dashboard {
    padding: 30px 40px 40px;
    transition: all 0.3s ease;
}

.dashboard.expanded {
    margin-left: 280px;
}

/* Título do dashboard */
.titulo-dash {
    display: flex;
    justify-content: center;
    align-content: center;
    text-align: center;
    color: #fff;
    font-size: 28px;
    margin-bottom: 30px;
    text-transform: uppercase;
    letter-spacing: 1px;
    position: relative;
    padding-bottom: 10px;
}

.titulo-dash::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 3px;
    background-color: #d00000;
}

/* Grid de cards CORRIGIDO */
.cards-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 30px;
    margin-bottom: 40px;
}

.card {
    background: 
        linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
    border-radius: 15px;
    padding: 30px 25px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    text-align: center;
    border: 1px solid #333;
    transition: transform 0.2s, box-shadow 0.2s;
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: center;
    min-height: 200px;
}

.card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: #d00000;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(208, 0, 0, 0.2);
}

.card h3 {
    color: #ccc;
    margin-bottom: 20px;
    font-size: 18px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}

.valor {
    font-size: 36px;
    font-weight: bold;
    color: #4CAF50;
    margin-bottom: 12px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.esgotado {
    color: #ff4d4d;
}

.positivo {
    font-size: 16px;
    color: #4CAF50;
    font-weight: 500;
}

.negativo {
    font-size: 16px;
    color: #ff4d4d;
    font-weight: 500;
}

.alerta {
    font-size: 16px;
    color: #ff4d4d;
    font-weight: 500;
}

/* ======= TABELA ABAIXO ======= */
.pedidos-container {
    width: 100%;
}

.titulo-pedidos {
    text-align: center;
    color: #fff;
    font-size: 24px;
    margin-bottom: 20px;
    text-transform: uppercase;
    letter-spacing: 1px;
    position: relative;
    padding-bottom: 10px;
}

.titulo-pedidos::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 3px;
    background-color: #d00000;
}

.tabela-pedidos {
    width: 100%;
    border-collapse: collapse;
    background: 
        linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
    border-radius: 12px;
    overflow: hidden;
    text-align: center;
    border: 1px solid #333;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.tabela-pedidos th {
    background: 
        linear-gradient(135deg, #d00000 0%, #b30000 100%);
    color: #fff;
    padding: 15px 10px;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
    font-size: 14px;
}

.tabela-pedidos td {
    padding: 15px 10px;
    border-bottom: 1px solid #333;
    vertical-align: middle;
}

.tabela-pedidos tr:hover {
    background-color: #222;
}

.tabela-pedidos tr:last-child td {
    border-bottom: none;
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
@media (max-width: 1200px) {
    .cards-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .dashboard {
        padding: 20px 20px 40px 20px;
    }
    
    .cards-grid {
        grid-template-columns: 1fr;
    }
    
    .user-card {
        padding: 20px;
    }
    
    .user-avatar img {
        width: 80px;
        height: 80px;
    }
    
    .tabela-pedidos {
        font-size: 14px;
    }
    
    .valor {
        font-size: 32px;
    }
    
    .card {
        padding: 25px 20px;
        min-height: 180px;
    }
    
    .card h3 {
        font-size: 16px;
    }
    
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
}

@media (max-width: 480px) {
    .dashboard {
        padding: 15px 15px 30px 15px;
    }
    
    .user-card {
        padding: 15px;
    }
    
    .card {
        padding: 20px 15px;
        min-height: 160px;
    }
    
    .valor {
        font-size: 28px;
    }
    
    .tabela-pedidos th,
    .tabela-pedidos td {
        padding: 12px 8px;
        font-size: 13px;
    }
    
    .user-name {
        font-size: 20px;
    }
    
    .nav-link {
        padding: 10px 15px;
        font-size: 14px;
    }
    
    .card h3 {
        font-size: 15px;
        margin-bottom: 15px;
    }
    
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
            <a href="dashboard.php" class="nav-link active">
                <i class="fas fa-home"></i> Início
            </a>
            <a href="pedidos.php" class="nav-link">
                <i class="fas fa-shopping-cart"></i> Pedidos
            </a>
            <a href="clientes.php" class="nav-link">
                <i class="fas fa-users"></i> Clientes
            </a>
            <a href="produtos.php" class="nav-link">
                <i class="fas fa-box"></i> Produtos
            </a>
            <a href="relatorios.php" class="nav-link">
                <i class="fas fa-chart-bar"></i> Relatórios
            </a>
        </div>
    </div>
</div>
    
    <!-- Conteúdo principal do dashboard -->
    <main class="dashboard">

        <h2 class="titulo-dash">DASHBOARD</h2>
        
        <!-- Cards de métricas CORRIGIDOS -->
        <div class="cards-grid">
            <div class="card">
                <h3>Receita Total (Mês)</h3>
                <p class="valor">R$ 15.847,50</p>
                <span class="positivo">
                    +12.5% vs mês anterior
                </span>
            </div>
            
            <div class="card">
                <h3>Pedidos Novos</h3>
                <p class="valor">42</p>
                <span class="positivo">
                    +8 pedidos novos hoje
                </span>
            </div>

            <div class="card">
                <h3>Novos Clientes</h3>
                <p class="valor">18</p>
                <span class="positivo">
                    +15.2% vs semana anterior
                </span>
            </div>
            
            <div class="card">
                <h3>Produtos Sem Estoque</h3>
                <p class="valor esgotado">7</p>
                <span class="alerta">
                    +2 vs ontem
                </span>
            </div>
        </div>

        <!-- Tabela de últimos pedidos -->
        <div class="pedidos-container">
            <h2 class="titulo-pedidos">ÚLTIMOS PEDIDOS</h2>

            <table class="tabela-pedidos">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Valor (R$)</th>
                        <th>Código do Item</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>#1042</td>
                        <td>João S.</td>
                        <td>R$ 1.249,90</td>
                        <td>CT-BIKE-001</td>
                    </tr>
                    <tr>
                        <td>#1041</td>
                        <td>Maria L.</td>
                        <td>R$ 899,50</td>
                        <td>CT-HELM-005</td>
                    </tr>
                    <tr>
                        <td>#1040</td>
                        <td>Pedro A.</td>
                        <td>R$ 2.150,00</td>
                        <td>CT-WHEEL-012</td>
                    </tr>
                    <tr>
                        <td>#1039</td>
                        <td>Ana C.</td>
                        <td>R$ 549,90</td>
                        <td>CT-GEAR-008</td>
                    </tr>
                    <tr>
                        <td>#1038</td>
                        <td>Carlos M.</td>
                        <td>R$ 1.799,00</td>
                        <td>CT-BIKE-003</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>
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
            const dashboard = document.querySelector('.dashboard');
            
            menuToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
                dashboard.classList.toggle('expanded');
                
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
            
            // Fechar menu ao clicar em um link (opcional)
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', function() {
                    sidebar.classList.remove('active');
                    dashboard.classList.remove('expanded');
                    menuToggle.querySelector('i').classList.remove('fa-times');
                    menuToggle.querySelector('i').classList.add('fa-bars');
                });
            });
        });
    </script>
</body>
</html>