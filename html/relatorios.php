<?php
session_start();
if (!isset($_SESSION['logado'])) {
    header("Location: login.php");
    exit;
}

include('config.php');

// Função para buscar dados do banco (APENAS PEDIDOS ENTREGUES)
function buscarDadosVendas($periodo, $categoria, $conn) {
    $dados = [
        'labels' => [],
        'itensData' => [],
        'vendasData' => [],
        'totalItens' => 0,
        'totalVendas' => 0,
        'variacaoItens' => 0,
        'variacaoVendas' => 0
    ];
    
    // Definir período da consulta
    $dataFim = date('Y-m-d 23:59:59');
    $dataInicio = date('Y-m-d 00:00:00', strtotime("-$periodo days"));
    
    // Construir query base - USANDO TABELA ENTREGUES
    $query = "SELECT 
                DATE(hora_entrega) as data,
                COUNT(*) as quantidade_itens,
                SUM(valor) as valor_total
              FROM entregues 
              WHERE hora_entrega BETWEEN ? AND ?";
    
    // Adicionar filtro de categoria se necessário
    if ($categoria !== 'todas') {
        $query .= " AND codigo_item LIKE ?";
    }
    
    $query .= " GROUP BY DATE(hora_entrega) ORDER BY data";
    
    $stmt = $conn->prepare($query);
    
    if ($categoria !== 'todas') {
        $categoriaLike = "%$categoria%";
        $stmt->bind_param("sss", $dataInicio, $dataFim, $categoriaLike);
    } else {
        $stmt->bind_param("ss", $dataInicio, $dataFim);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Processar resultados
    while ($row = $result->fetch_assoc()) {
        $dados['labels'][] = date('d/m', strtotime($row['data']));
        $dados['itensData'][] = (int)$row['quantidade_itens'];
        $dados['vendasData'][] = (float)$row['valor_total'];
        $dados['totalItens'] += (int)$row['quantidade_itens'];
        $dados['totalVendas'] += (float)$row['valor_total'];
    }
    
    // Se não houver dados, criar arrays vazios para evitar erros no JavaScript
    if (empty($dados['labels'])) {
        $dados['labels'] = [];
        $dados['itensData'] = [];
        $dados['vendasData'] = [];
    }
    
    // Calcular variação em relação ao período anterior
    $dados = calcularVariacaoPeriodoAnterior($dados, $periodo, $categoria, $conn);
    
    return $dados;
}

function calcularVariacaoPeriodoAnterior($dadosAtuais, $periodo, $categoria, $conn) {
    $periodoAnterior = $periodo;
    $dataFimAnterior = date('Y-m-d 23:59:59', strtotime("-$periodo days"));
    $dataInicioAnterior = date('Y-m-d 00:00:00', strtotime("-" . ($periodo * 2) . " days"));
    
    $query = "SELECT 
                COUNT(*) as quantidade_itens,
                SUM(valor) as valor_total
              FROM entregues 
              WHERE hora_entrega BETWEEN ? AND ?";
    
    if ($categoria !== 'todas') {
        $query .= " AND codigo_item LIKE ?";
    }
    
    $stmt = $conn->prepare($query);
    
    if ($categoria !== 'todas') {
        $categoriaLike = "%$categoria%";
        $stmt->bind_param("sss", $dataInicioAnterior, $dataFimAnterior, $categoriaLike);
    } else {
        $stmt->bind_param("ss", $dataInicioAnterior, $dataFimAnterior);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $totalItensAnterior = $row['quantidade_itens'] ?: 1;
    $totalVendasAnterior = $row['valor_total'] ?: 1;
    
    // Calcular variações percentuais
    $dadosAtuais['variacaoItens'] = round((($dadosAtuais['totalItens'] - $totalItensAnterior) / $totalItensAnterior) * 100, 1);
    $dadosAtuais['variacaoVendas'] = round((($dadosAtuais['totalVendas'] - $totalVendasAnterior) / $totalVendasAnterior) * 100, 1);
    
    return $dadosAtuais;
}

// Processar filtros
$periodo = $_GET['periodo'] ?? 30;
$categoria = $_GET['categoria'] ?? 'todas';

// Buscar dados do banco
$dados = buscarDadosVendas($periodo, $categoria, $conn);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatórios - Ciclotech</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

/* ======= ESTILOS DOS RELATÓRIOS ======= */
.titulo-relatorios {
    text-align: center;
    color: #fff;
    font-size: 28px;
    margin-bottom: 30px;
    text-transform: uppercase;
    letter-spacing: 1px;
    position: relative;
    padding-bottom: 10px;
}

.titulo-relatorios::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 3px;
    background-color: #d00000;
}

/* Controles de filtro */
.controles {
    background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 30px;
    display: flex;
    gap: 20px;
    align-items: center;
    flex-wrap: wrap;
    border: 1px solid #333;
}

.grupo-filtro {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.grupo-filtro label {
    color: #fff;
    font-weight: 600;
    font-size: 14px;
}

.grupo-filtro select {
    background: #333;
    color: #fff;
    border: 1px solid #555;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 14px;
    min-width: 150px;
}

.controles button {
    background: #4CAF50;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
    align-self: flex-end;
}

.controles button:hover {
    background: #45a049;
    transform: translateY(-2px);
}

/* Cards de resumo */
.cards-resumo {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.card-resumo {
    background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
    padding: 25px;
    border-radius: 10px;
    text-align: center;
    border: 1px solid #333;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    transition: transform 0.3s ease;
}

.card-resumo:hover {
    transform: translateY(-5px);
}

.card-resumo.primary {
    border-top: 4px solid #3498db;
}

.card-resumo.success {
    border-top: 4px solid #27ae60;
}

.rotulo-resumo {
    color: #ccc;
    font-size: 14px;
    margin-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.valor-resumo {
    color: #fff;
    font-size: 32px;
    font-weight: bold;
    margin-bottom: 10px;
}

.variacao-resumo {
    font-size: 14px;
    font-weight: 600;
}

.variacao-positiva {
    color: #27ae60;
}

.variacao-negativa {
    color: #e74c3c;
}

/* Grid de relatórios */
.relatorios-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
    gap: 30px;
    margin-bottom: 30px;
}

.card-relatorio {
    background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
    border-radius: 10px;
    border: 1px solid #333;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    overflow: hidden;
}

.card-header {
    background: linear-gradient(135deg, #d00000 0%, #b30000 100%);
    padding: 20px;
    text-align: center;
}

.card-titulo {
    color: #fff;
    font-size: 18px;
    font-weight: 600;
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.chart-container {
    padding: 20px;
    height: 300px;
    position: relative;
}

/* Estatísticas adicionais */
.estatisticas-adicionais {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
    padding: 25px;
    border-radius: 10px;
    border: 1px solid #333;
}

.estatistica-item {
    text-align: center;
    padding: 15px;
}

.estatistica-valor {
    color: #fff;
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 5px;
}

.estatistica-rotulo {
    color: #ccc;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Mensagem de carregamento */
.carregando {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: #999;
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
    
    .relatorios-grid {
        grid-template-columns: 1fr;
    }
    
    .controles {
        flex-direction: column;
        align-items: stretch;
    }
    
    .grupo-filtro {
        width: 100%;
    }
    
    .controles button {
        align-self: stretch;
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
    
    .cards-resumo {
        grid-template-columns: 1fr;
    }
    
    .relatorios-grid {
        grid-template-columns: 1fr;
    }
    
    .chart-container {
        height: 250px;
    }
    
    .estatisticas-adicionais {
        grid-template-columns: 1fr;
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
                <a href="produtos.php" class="nav-link">
                    <i class="fas fa-box"></i> Produtos
                </a>
                <a href="relatorios.php" class="nav-link active">
                    <i class="fas fa-chart-bar"></i> Relatórios
                </a>
            </div>
        </div>
    </div>

    <!-- Conteúdo principal -->
    <div class="content-area">
        <h1 class="titulo-relatorios">Relatórios de Vendas</h1>

        <div class="controles">
            <div class="grupo-filtro">
                <label for="periodo">Período:</label>
                <select id="periodo">
                    <option value="7" <?= $periodo == 7 ? 'selected' : '' ?>>7 dias</option>
                    <option value="30" <?= $periodo == 30 ? 'selected' : '' ?>>30 dias</option>
                    <option value="90" <?= $periodo == 90 ? 'selected' : '' ?>>3 meses</option>
                    <option value="180" <?= $periodo == 180 ? 'selected' : '' ?>>6 meses</option>
                    <option value="365" <?= $periodo == 365 ? 'selected' : '' ?>>1 ano</option>
                </select>
            </div>

            <div class="grupo-filtro">
                <label for="categoria">Categoria:</label>
                <select id="categoria">
                    <option value="todas" <?= $categoria == 'todas' ? 'selected' : '' ?>>Todas</option>
                    <option value="BIKE" <?= $categoria == 'BIKE' ? 'selected' : '' ?>>Bikes</option>
                    <option value="PN" <?= $categoria == 'PN' ? 'selected' : '' ?>>Pneus</option>
                    <option value="TR" <?= $categoria == 'TR' ? 'selected' : '' ?>>Trajes</option>
                    <option value="EQ" <?= $categoria == 'EQ' ? 'selected' : '' ?>>Equipamentos</option>
                </select>
            </div>

            <button onclick="atualizarFiltros()">
                Atualizar
            </button>
        </div>

        <div class="cards-resumo">
            <div class="card-resumo primary">
                <div class="rotulo-resumo">Itens Vendidos</div>
                <div class="valor-resumo"><?= number_format($dados['totalItens'], 0, ',', '.') ?></div>
                <div class="variacao-resumo <?= $dados['variacaoItens'] >= 0 ? 'variacao-positiva' : 'variacao-negativa' ?>">
                    <?= $dados['variacaoItens'] >= 0 ? '↑' : '↓' ?> <?= abs($dados['variacaoItens']) ?>%
                </div>
            </div>

            <div class="card-resumo success">
                <div class="rotulo-resumo">Vendas Totais</div>
                <div class="valor-resumo">R$ <?= number_format($dados['totalVendas'], 2, ',', '.') ?></div>
                <div class="variacao-resumo <?= $dados['variacaoVendas'] >= 0 ? 'variacao-positiva' : 'variacao-negativa' ?>">
                    <?= $dados['variacaoVendas'] >= 0 ? '↑' : '↓' ?> <?= abs($dados['variacaoVendas']) ?>%
                </div>
            </div>
        </div>

        <div class="relatorios-grid">
            <div class="card-relatorio">
                <div class="card-header">
                    <h2 class="card-titulo">Itens Vendidos</h2>
                </div>
                <div class="chart-container">
                    <canvas id="graficoItens"></canvas>
                </div>
            </div>

            <div class="card-relatorio">
                <div class="card-header">
                    <h2 class="card-titulo">Valor de Vendas</h2>
                </div>
                <div class="chart-container">
                    <canvas id="graficoVendas"></canvas>
                </div>
            </div>
        </div>

        <div class="estatisticas-adicionais">
            <div class="estatistica-item">
                <div class="estatistica-valor"><?= number_format($dados['totalItens'] / max($periodo, 1), 1) ?></div>
                <div class="estatistica-rotulo">Itens/dia</div>
            </div>
            <div class="estatistica-item">
                <div class="estatistica-valor">R$ <?= number_format($dados['totalVendas'] / max($periodo, 1), 2, ',', '.') ?></div>
                <div class="estatistica-rotulo">Vendas/dia</div>
            </div>
            <div class="estatistica-item">
                <div class="estatistica-valor">R$ <?= number_format($dados['totalVendas'] / max($dados['totalItens'], 1), 2, ',', '.') ?></div>
                <div class="estatistica-rotulo">Ticket médio</div>
            </div>
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
        // Dados do PHP para JavaScript
        const dadosRelatorio = <?= json_encode($dados) ?>;
        
        console.log('Dados do PHP:', dadosRelatorio);
        
        // Inicializar gráficos
        function inicializarGraficos() {
            // Verificar se há dados
            if (!dadosRelatorio.labels || dadosRelatorio.labels.length === 0) {
                console.log('Nenhum dado encontrado para exibir nos gráficos');
                document.getElementById('graficoItens').innerHTML = '<div class="carregando">Nenhum dado encontrado</div>';
                document.getElementById('graficoVendas').innerHTML = '<div class="carregando">Nenhum dado encontrado</div>';
                return;
            }
            
            // Gráfico de itens vendidos
            const ctxItens = document.getElementById('graficoItens').getContext('2d');
            new Chart(ctxItens, {
                type: 'bar',
                data: {
                    labels: dadosRelatorio.labels,
                    datasets: [{
                        label: 'Itens Vendidos',
                        data: dadosRelatorio.itensData,
                        backgroundColor: 'rgba(52, 152, 219, 0.8)',
                        borderColor: 'rgba(52, 152, 219, 1)',
                        borderWidth: 1,
                        borderRadius: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            },
                            ticks: {
                                color: '#fff',
                                font: {
                                    size: 10
                                }
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            },
                            ticks: {
                                color: '#fff',
                                font: {
                                    size: 9
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            bodyFont: {
                                size: 11
                            }
                        }
                    }
                }
            });

            // Gráfico de vendas
            const ctxVendas = document.getElementById('graficoVendas').getContext('2d');
            new Chart(ctxVendas, {
                type: 'line',
                data: {
                    labels: dadosRelatorio.labels,
                    datasets: [{
                        label: 'Valor de Vendas (R$)',
                        data: dadosRelatorio.vendasData,
                        backgroundColor: 'rgba(39, 174, 96, 0.2)',
                        borderColor: 'rgba(39, 174, 96, 1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            },
                            ticks: {
                                color: '#fff',
                                font: {
                                    size: 10
                                },
                                callback: function(value) {
                                    return 'R$ ' + value.toLocaleString('pt-BR');
                                }
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            },
                            ticks: {
                                color: '#fff',
                                font: {
                                    size: 9
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            bodyFont: {
                                size: 11
                            },
                            callbacks: {
                                label: function(context) {
                                    return 'R$ ' + context.parsed.y.toLocaleString('pt-BR', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                }
                            }
                        }
                    }
                }
            });
        }

        // Função para atualizar filtros
        function atualizarFiltros() {
            const periodo = document.getElementById('periodo').value;
            const categoria = document.getElementById('categoria').value;
            
            window.location.href = `relatorios.php?periodo=${periodo}&categoria=${categoria}`;
        }

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

            // Inicializar gráficos
            inicializarGraficos();
        });
    </script>
</body>
</html>