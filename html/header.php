<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<br>
    <header>
        <div class="lado-esquerdo">
            <a href="../html/logout.php">SAIR</a>
        </div>

        <div class="logo">
            <img src="../img/logo.png" alt="Ciclotech">
        </div>

        <div class="carrinho">
        </div>
</header>
