<?php
session_start();
if (!isset($_SESSION['logado'])) {
    header("Location: login.php");
    exit;
}

include 'config.php';

// ========== FUNÇÃO PARA ABREVIAR NOMES ==========
function abreviarNome($nome) {
    $partes = explode(" ", trim($nome));
    if (count($partes) === 1) return $nome;
    return $partes[0] . " " . substr(end($partes), 0, 1) . ".";
}

// ========== CANCELAR PEDIDO ==========
if (isset($_GET['cancelar'])) {
    $id = intval($_GET['cancelar']);
    $conn->query("DELETE FROM pedidos WHERE id = $id");
    header("Location: pedidos.php");
    exit;
}

// ========== ENVIAR PEDIDO ==========
if (isset($_GET['enviar'])) {
    $id = intval($_GET['enviar']);
    $sql = "SELECT * FROM pedidos WHERE id = $id";
    $pedido = $conn->query($sql)->fetch_assoc();

    if ($pedido) {
        $stmt = $conn->prepare("INSERT INTO em_preparo (id_cliente, valor, codigo_item, hora_inicio) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("ids", $pedido['id_cliente'], $pedido['valor'], $pedido['codigo_item']);
        $stmt->execute();
        $conn->query("DELETE FROM pedidos WHERE id = $id");
    }
    header("Location: pedidos.php");
    exit;
}

// ========== MOVER AUTOMATICAMENTE PARA ENTREGUES ==========
$conn->query("
    INSERT INTO entregues (id_cliente, valor, codigo_item, hora_entrega)
    SELECT id_cliente, valor, codigo_item, NOW()
    FROM em_preparo
    WHERE TIMESTAMPDIFF(SECOND, hora_inicio, NOW()) >= 10
");
$conn->query("DELETE FROM em_preparo WHERE TIMESTAMPDIFF(SECOND, hora_inicio, NOW()) >= 10");

// ========== CONSULTAS ==========
$pendentes = $conn->query("SELECT pedidos.*, clientes.nome FROM pedidos JOIN clientes ON pedidos.id_cliente = clientes.id ORDER BY pedidos.id DESC");
$preparo = $conn->query("SELECT em_preparo.*, clientes.nome FROM em_preparo JOIN clientes ON em_preparo.id_cliente = clientes.id ORDER BY em_preparo.id DESC");
$entregues = $conn->query("SELECT entregues.*, clientes.nome FROM entregues JOIN clientes ON entregues.id_cliente = clientes.id ORDER BY entregues.id DESC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Pedidos - Ciclotech</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet">
  <script>
    // Atualiza a página a cada 5s
    setInterval(() => location.reload(), 5000);
  </script>
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

/* ======= ESTILOS DOS PEDIDOS ======= */
.titulo-pedidos {
    text-align: center;
    color: #fff;
    font-size: 28px;
    margin: 40px 0 20px 0;
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
    background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
    border-radius: 12px;
    overflow: hidden;
    text-align: center;
    border: 1px solid #333;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    margin-bottom: 40px;
}

.tabela-pedidos th {
    background: linear-gradient(135deg, #d00000 0%, #b30000 100%);
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

/* Botões de ação */
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
}

.btn-excluir:hover {
    background: #b91c1c;
    transform: translateY(-2px);
}

/* Estilo para informações de horário */
.horario-info {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2px;
}

.horario-dia {
    font-size: 12px;
    color: #ccc;
}

.horario-hora {
    font-size: 14px;
    font-weight: 600;
    color: #fff;
}

.horario-info.previsao .horario-hora {
    color: #4CAF50;
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
    
    .tabela-pedidos {
        font-size: 14px;
    }
    
    .btn-editar, .btn-excluir {
        padding: 6px 12px;
        font-size: 12px;
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
    
    .tabela-pedidos th,
    .tabela-pedidos td {
        padding: 12px 8px;
        font-size: 13px;
    }
    
    .titulo-pedidos {
        font-size: 24px;
        margin: 30px 0 15px 0;
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
                <a href="pedidos.php" class="nav-link active">
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

    <!-- Conteúdo principal -->
    <div class="content-area">
        <!-- ========== PEDIDOS PENDENTES ========== -->
        <h2 class="titulo-pedidos">PEDIDOS PENDENTES</h2>
        <table class="tabela-pedidos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>CLIENTE</th>
                    <th>VALOR</th>
                    <th>CÓDIGO</th>
                    <th>AÇÕES</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($pendentes->num_rows > 0): ?>
                    <?php while($row = $pendentes->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= $row['id'] ?></td>
                            <td><?= abreviarNome($row['nome']) ?></td>
                            <td>R$ <?= number_format($row['valor'], 2, ',', '.') ?></td>
                            <td><?= htmlspecialchars($row['codigo_item']) ?></td>
                            <td>
                                <a href="pedidos.php?enviar=<?= $row['id'] ?>" class="btn-editar">Enviar</a>
                                <a href="pedidos.php?cancelar=<?= $row['id'] ?>" class="btn-excluir" onclick="return confirm('Cancelar este pedido?')">Cancelar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">Nenhum pedido pendente.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- ========== PEDIDOS EM PREPARO ========== -->
        <h2 class="titulo-pedidos">PEDIDOS EM PREPARO</h2>
        <table class="tabela-pedidos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>CLIENTE</th>
                    <th>VALOR</th>
                    <th>CÓDIGO</th>
                    <th>ENVIADO EM</th>
                    <th>PREVISÃO DE CHEGADA</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($preparo->num_rows > 0): ?>
                    <?php while($row = $preparo->fetch_assoc()): ?>
                        <?php
                        // Calcular horários
                        $horaEnvio = new DateTime($row['hora_inicio']);
                        $horaPrevisao = clone $horaEnvio;
                        $horaPrevisao->modify('+10 seconds'); // 10 segundos após o envio
                        ?>
                        <tr>
                            <td>#<?= $row['id'] ?></td>
                            <td><?= abreviarNome($row['nome']) ?></td>
                            <td>R$ <?= number_format($row['valor'], 2, ',', '.') ?></td>
                            <td><?= htmlspecialchars($row['codigo_item']) ?></td>
                            <td>
                                <div class="horario-info">
                                    <div class="horario-dia"><?= $horaEnvio->format('d/m/Y') ?></div>
                                    <div class="horario-hora"><?= $horaEnvio->format('H:i:s') ?></div>
                                </div>
                            </td>
                            <td>
                                <div class="horario-info previsao">
                                    <div class="horario-dia"><?= $horaPrevisao->format('d/m/Y') ?></div>
                                    <div class="horario-hora"><?= $horaPrevisao->format('H:i:s') ?></div>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6">Nenhum pedido em preparo.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- ========== PEDIDOS ENTREGUES ========== -->
        <h2 class="titulo-pedidos">PEDIDOS ENTREGUES</h2>
        <table class="tabela-pedidos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>CLIENTE</th>
                    <th>VALOR</th>
                    <th>CÓDIGO</th>
                    <th>ENTREGUE EM</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($entregues->num_rows > 0): ?>
                    <?php while($row = $entregues->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= $row['id'] ?></td>
                            <td><?= abreviarNome($row['nome']) ?></td>
                            <td>R$ <?= number_format($row['valor'], 2, ',', '.') ?></td>
                            <td><?= htmlspecialchars($row['codigo_item']) ?></td>
                            <td>
                                <div class="horario-info">
                                    <div class="horario-dia"><?= date('d/m/Y', strtotime($row['hora_entrega'])) ?></div>
                                    <div class="horario-hora"><?= date('H:i:s', strtotime($row['hora_entrega'])) ?></div>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">Nenhum pedido entregue ainda.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
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
</script>
</body>
</html>