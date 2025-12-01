<?php
session_start();
require_once __DIR__ . '/sessao.php';
require_once __DIR__ . '/flash.php';

// Verifica se já está logado como usuário normal
if (!empty($_SESSION['usuario_id'])) {
    header('Location: home.php');
    exit;
}

// Verifica se já está logado como administrador
if (!empty($_SESSION['logado']) && $_SESSION['logado'] === true) {
    header('Location: dashboard.php');
    exit;
}

$erro = flash_get();

// Processamento do formulário de login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lista de contas de administrador válidas
    $admins = [
        'cicloadmV@gmail.com' => 'adm123',
        'cicloadmH@gmail.com' => 'adm123',
        'cicloadmG@gmail.com' => 'adm123',
        'cicloadmB@gmail.com' => 'adm123'
    ];

    // Mapeamento de e-mails para nomes
    $nomes_admin = [
        'cicloadmV@gmail.com' => 'Vitor Vaz',
        'cicloadmH@gmail.com' => 'Henrique',
        'cicloadmG@gmail.com' => 'Giovanni',
        'cicloadmB@gmail.com' => 'Gabriel Bastos'
    ];

    // Mapeamento de e-mails para fotos
    $fotos_admin = [
        'cicloadmV@gmail.com' => 'VITOR.jpeg',
        'cicloadmH@gmail.com' => 'HENRIQUE.jpeg',
        'cicloadmG@gmail.com' => 'GIOVANNI.jpeg',
        'cicloadmB@gmail.com' => 'BASTOS.jpg'
    ];

    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    // Verifica se é um administrador
    if (isset($admins[$email]) && $admins[$email] === $senha) {
        $_SESSION['logado'] = true;
        $_SESSION['usuario'] = $email;
        $_SESSION['admin_nome'] = $nomes_admin[$email];
        $_SESSION['admin_foto'] = $fotos_admin[$email];

        // Redireciona para o dashboard
        header('Location: dashboard.php');
        exit;
    } else {
        // Se não for admin, verifica se é usuário normal no banco de dados
        include('config.php');
        
        $stmt = $conn->prepare("SELECT id, nome, senha_hash FROM clientes WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $usuario = $result->fetch_assoc();
            
            // CORREÇÃO: Verifica a senha usando password_verify com a coluna correta
            if (password_verify($senha, $usuario['senha_hash'])) {
                // Login bem-sucedido para usuário normal
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_email'] = $email;
                
                header('Location: home.php');
                exit;
            } else {
                flash_set('Senha incorreta!');
                header('Location: login.php');
                exit;
            }
        } else {
            flash_set('E-mail não encontrado!');
            header('Location: login.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ciclotech - Login</title>
    <link rel="stylesheet" href="../css/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Poppins&display=swap" rel="stylesheet">
</head>
<body>
<header>
    <div class="lado-esquerdo">
        <a href="../html/homepage.php">HOME</a>
    </div>
    <div class="logo"><img src="../img/logo.png" alt="Ciclotech"></div>
        <div class="carrinho">
            <a href="contato.html" class="botaoContato" title="Fale Conosco">
                <img id = "ImgContato" src="../img/contato.png" alt="Contato"></a>
        </div>
</header>
<div class="login-card">
    <div class="login-container">
<h1 class="titulo-login" style="text-align:center;">LOGIN</h1>
<br>
    <?php if ($erro): ?>
        <p style="color:red; text-align:center;"><?=htmlspecialchars($erro)?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="email" name="email" placeholder="Endereço de E-mail" required>
        <input type="password" name="senha" placeholder="Senha" required>
        <button type="submit" name="login" class="continuar">Continuar</button>
    </form>

    <div class="divider">OU UTILIZE SUA CONTA</div>

    <button class="social-btn facebook">
        <img src="../img/google.png" alt="Google"><a href="https://accounts.google.com/v3/signin/identifier?continue=https://accounts.google.com/signin/chrome/sync/finish?est%3DAJFqneRfRX6HOHlKg_OJZS7KLXo_W-uHdGFaqiQDU-QbTvdBBYenlYtpde7AHTOQeecAv2k_L7gV5LCf3N7IyA%26continue%3Dhttps://www.google.com/&ssp=1&theme=mn&flowName=GlifDesktopChromeSync&dsh=S-1254260731:1761564002253477" style="text-decoration: none;">Google</a>
    </button>
    <button class="social-btn facebook">
        <img src="../img/facebook.png" alt="Facebook"><a href="https://www.facebook.com/help/ipreporting/report/copyright" style="text-decoration: none;">Facebook</a>
    </button>

    <a href="cadastro.php"style="text-decoration: none;"><button class="continuar">Cadastrar-se</button></a>

    <div class="mini-footer">Protegido por reCAPTCHA - <a href="#">Privacidade</a> | <a href="#">Condições</a></div>
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
</body>
</html>