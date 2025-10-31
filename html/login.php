<?php
require_once __DIR__ . '/sessao.php';
require_once __DIR__ . '/flash.php';
if (!empty($_SESSION['usuario'])) {
    header('Location: home.php');
    exit;
}
$erro = flash_get();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ciclotech - Login</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
<header>
    <div class="lado-esquerdo">
        <a href="../html/homepage.php">HOME</a>
    </div>
    <div class="logo"><img src="../img/logo.png" alt="Ciclotech"></div>
        <div class="carrinho">
            <a href="../html/usuario.php"><img src="../img/user.png" style="width: 25px; margin-right: 20px;"></a>
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

    <form method="POST" action="autenticar.php">
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

    <a href="cadastro.php"style="text-decoration: none;"><button class="cadastrar">Cadastrar-se</button></a>

    <div class="mini-footer">Protegido por reCAPTCHA - <a href="#">Privacidade</a> | <a href="#">Condições</a></div>
</div>
</div>

    <footer>
        <div class="footer-top">
            <span>Entregamos para o Mundo inteiro</span>
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
                    <li><a href="index.html">Cilcotech</a></li>
                    <li><a href="#">Política De Privacidade</a></li>
                    <li><a href="#">Trocas e Devoluções</a></li>
                </ul>
            </div>
            <div class="footer-section footer-contact">
                <h3>Atendimento</h3>
                <p>Segunda a Sexta<br>10:00 ás 16:00</p>
                <br>
                <h3>Contato</h3>
                <p>(12)1234-5678</p>
                <p>(12)1234-5678</p>
                <h3>Redes Sociais</h3>
                <div class="footer-social">
                    <img src="../img/instagram.png" alt="Instagram">
                    <img src="../img/facebook.png" alt="Facebook">
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
