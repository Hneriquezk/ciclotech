<?php
require_once __DIR__ . '/sessao.php';
require_once __DIR__ . '/flash.php';
$erro = flash_get();
$sucesso = null;
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ciclotech</title>
    <link rel="stylesheet" href="../css/cadastrar.css">
    <link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Poppins&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="lado-esquerdo">
            <a href="../html/login.php">LOGIN</a>
        </div>

        <div class="logo">
            <img src="../img/logo.png" alt="Ciclotech">
        </div>
          <div class="carrinho">
            <a href="../html/usuario.php"><img src="../img/user.png" style="width: 25px; margin-right: 20px;"></a>
            <a href="contato.html" class="botaoContato" title="Fale Conosco">
                <img id = "ImgContato" src="../img/contato.png" alt="Contato"></a>
        </div>

    </header>
    <br><br>

<div class="container">
  <h2 class="titulo-cadastro">CADASTRO</h2>

  <?php if ($erro): ?>
    <div class="alert-error">
      <?=htmlspecialchars($erro)?>
    </div>
  <?php endif; ?>

  <form method="POST" action="cadastrar.php" class="form-cadastro">
    <div class="form-group">
      <input type="text" name="nome" placeholder="Nome completo" required class="form-input">
    </div>

    <div class="form-group">
      <select name="sexo" required class="form-select">
        <option value="">Selecione o gênero</option>
        <option value="Masculino">Masculino</option>
        <option value="Feminino">Feminino</option>
        <option value="Outro">Outro</option>
      </select>
    </div>

    <div class="form-row">
      <div class="form-group">
        <input type="text" name="cpf" placeholder="CPF" required class="form-input">
      </div>
      <div class="form-group">
        <input type="text" name="telefone" placeholder="Telefone" required class="form-input">
      </div>
    </div>

    <div class="form-group">
      <input type="date" name="nascimento" required class="form-input">
    </div>

    <div class="form-group">
      <input type="email" name="email" placeholder="E-mail" required class="form-input">
    </div>

    <div class="form-row">
      <div class="form-group">
        <input type="password" name="senha" placeholder="Senha" required class="form-input">
      </div>
      <div class="form-group">
        <input type="password" name="confirmar" placeholder="Confirmar senha" required class="form-input">
      </div>
    </div>

    <button type="submit" name="cadastrar" class="btn-submit">CRIAR CONTA</button>
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
