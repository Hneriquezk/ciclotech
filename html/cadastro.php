<?php
require_once __DIR__ . '/sessao.php';
require_once __DIR__ . '/flash.php';
$erro = flash_get();
$sucesso = null;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Ciclotech - Cadastro</title>
  <link rel="stylesheet" href="../css/cadastrar.css">
</head>
<body>
    <header>
        <div class="lado-esquerdo">
            <a href="../html/login.php">LOGIN</a>
            <a href="../html/contato.html">CONTATO</a>
            <a href="../html/homepage.php">HOME</a>
        </div>

        <div class="logo">
            <img src="../img/logo.png" alt="Ciclotech">
        </div>

        <div class="carrinho">
            <img src="../img/carrinho.png" alt="Carrinho">
        </div>
    </header>
   <nav>
        <a href="#">Bicicletas/Quadros</a>
        <span>|</span>
        <a href="#">Capacetes</a>
        <span>|</span>
        <a href="#">Rodas</a>
        <span>|</span>
        <a href="#">Pneus/Câmaras</a>
        <span>|</span>
        <a href="#">Óculos</a>
        <span>|</span>
        <a href="#">Luvas</a>
        <span>|</span>
        <a href="#">Vestuário</a>
        <span>|</span>
        <a href="#">Kit Limpeza</a>
    </nav>
    <br><br>

<div class="container">
  <h2>CADASTRO</h2>

  <?php if ($erro): ?>
    <p style="color:red; text-align:center; margin-bottom:10px;"><?=htmlspecialchars($erro)?></p>
  <?php endif; ?>

  <form method="POST" action="cadastrar.php">
    <input type="text" name="nome" placeholder="Digite seu nome" required>
    <select name="sexo" required>
            <option value="">Selecione</option>
            <option value="Masculino">Masculino</option>
            <option value="Feminino">Feminino</option>
            <option value="Outro">Outro</option>
    </select>
    <input type="text" name="cpf" placeholder="___.___.___-__" required>
    <input type="text" name="telefone" placeholder="(__) _____-____" required>
    <input type="date" name="nascimento" required>
    <input type="email" name="email" placeholder="Digite seu e-mail" required>
    <input type="password" name="senha" placeholder="Digite uma senha" required>
    <input type="password" name="confirmar" placeholder="Confirme a senha" required>
    <button type="submit" name="cadastrar" class="btn">CRIAR CONTA</button>
  </form>
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
