<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/flash.php';
require_login();
$usuario = $_SESSION['usuario'] ?? null;
$msg = flash_get();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - Ciclotech</title>
    <link rel="stylesheet" href="../css/usuario.css">
</head>

<body>
    <header>
        <div class="lado-esquerdo">
            <a href="homepage.php">HOME</a>
        </div>

        <div class="logo">
            <img src="../img/logo.png" alt="Ciclotech">
        </div>

        <div class="carrinho">
            <img src="../img/carrinho.png" alt="Carrinho" style="width: 25px; height: 25px; margin-right: 20px;">
            <a href="contato.html" class="botao-contato-fixo" title="Fale Conosco">
                <img src="../img/contato.png" alt="Contato"></a>
        </div>
        <!-- ================= CARRINHO LATERAL ================= -->
        <div id="carrinho-lateral" class="carrinho-lateral">
            <div class="carrinho-header">
                <h2>Carrinho</h2>
                <button id="fechar-carrinho">&times;</button>
            </div>
            <div id="carrinho-itens" class="carrinho-itens">
                <p>Seu carrinho está vazio</p>
            </div>
            <div class="carrinho-footer">
                <div class="total">Total: <span id="carrinho-total">R$0,00</span></div>
                <button id="finalizar-compra">Finalizar Compra</button>
            </div>
        </div>
    </header>

    <main class="container">
        <section class="perfil">
            <h2>Meu Perfil</h2>

            <div class="avatar-section">
                <img src="<?php echo $avatar; ?>" alt="Avatar do Usuário" class="avatar">
                <form action="../upload_avatar.php" method="POST" enctype="multipart/form-data">
                    <label for="avatar-upload" class="botao-upload">Alterar Foto</label>
                    <input type="file" id="avatar-upload" name="avatar" accept="image/*" style="display: none;"
                        onchange="this.form.submit()">
                </form>
            </div>

            <div class="dados-usuario">
                <p><strong>Nome:</strong>
                    <?php echo htmlspecialchars($nome); ?>
                </p>
                <p><strong>E-mail:</strong>
                    <?php echo htmlspecialchars($email); ?>
                </p>
                <p><strong>Idade:</strong>
                    <?php echo htmlspecialchars($idade); ?>
                </p>
                <p><strong>Sexo:</strong>
                    <?php echo htmlspecialchars($sexo); ?>
                </p>
                <p><strong>CPF:</strong>
                    <?php echo htmlspecialchars($cpf); ?>
                </p>
                <p><strong>Telefone:</strong>
                    <?php echo htmlspecialchars($telefone); ?>
                </p>
            </div>

            <hr>

            <div class="trocar-senha">
                <h3>Trocar Senha</h3>
                <form action="../trocar_senha.php" method="POST">
                    <input type="password" name="senha_atual" placeholder="Senha atual" required>
                    <input type="password" name="nova_senha" placeholder="Nova senha" required>
                    <input type="password" name="confirmar_senha" placeholder="Confirmar nova senha" required>
                    <button type="submit">Atualizar Senha</button>
                </form>
            </div>
        </section>
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
    <script>
        // ======================= CARRINHO LATERAL =======================

        // Seleciona os elementos principais do carrinho
        const carrinho = document.getElementById("carrinho-lateral");
        const fecharCarrinho = document.getElementById("fechar-carrinho");
        const carrinhoItens = document.getElementById("carrinho-itens");
        const carrinhoTotal = document.getElementById("carrinho-total");

        // --- Abre o carrinho ao clicar no ícone ---
        document.querySelector(".carrinho img").addEventListener("click", () => {
            carrinho.classList.add("ativo");
            renderCarrinho(); // Atualiza os itens exibidos
        });

        // --- Fecha o carrinho ao clicar no "X" ---
        fecharCarrinho.addEventListener("click", () => carrinho.classList.remove("ativo"));

        // --- Função: Obtém os itens do carrinho armazenados no localStorage ---
        function getCarrinho() {
            return JSON.parse(localStorage.getItem("carrinho")) || [];
        }

        // --- Função: Salva os itens no localStorage ---
        function salvarCarrinho(itens) {
            localStorage.setItem("carrinho", JSON.stringify(itens));
        }

        // --- Função: Renderiza os produtos dentro do carrinho lateral ---
        function renderCarrinho() {
            const itens = getCarrinho();
            carrinhoItens.innerHTML = "";

            if (itens.length === 0) {
                carrinhoItens.innerHTML = "<p>Seu carrinho está vazio</p>";
                carrinhoTotal.innerText = "R$0,00";
                return;
            }

            let total = 0;

            itens.forEach((item, index) => {
                total += item.preco * item.quantidade;

                const div = document.createElement("div");
                div.classList.add("item-carrinho");
                div.innerHTML = `
            <img src="${item.img}" alt="${item.nome}">
            <div class="item-info">
                <h4>${item.nome}</h4>
                <p>R$${item.preco.toFixed(2)}</p>
                <div class="quantidade">
                    <button onclick="alterarQtd(${index}, -1)">-</button>
                    <span>${item.quantidade}</span>
                    <button onclick="alterarQtd(${index}, 1)">+</button>
                </div>
                <span class="remover" onclick="removerItem(${index})">Remover</span>
            </div>
        `;
                carrinhoItens.appendChild(div);
            });

            carrinhoTotal.innerText = `R$${total.toFixed(2)}`;
        }

        // --- Função: Altera a quantidade de um item no carrinho ---
        function alterarQtd(index, delta) {
            let itens = getCarrinho();
            itens[index].quantidade += delta;

            if (itens[index].quantidade <= 0) itens.splice(index, 1);

            salvarCarrinho(itens);
            renderCarrinho();
        }

        // --- Função: Remove completamente um item do carrinho ---
        function removerItem(index) {
            let itens = getCarrinho();
            itens.splice(index, 1);
            salvarCarrinho(itens);
            renderCarrinho();
        }

        // --- Evento: Finalizar compra e ir para página de pagamento ---
        document.getElementById("finalizar-compra").addEventListener("click", () => {
            const itens = getCarrinho();

            if (itens.length === 0) {
                alert("Seu carrinho está vazio!");
                return;
            }

            const total = itens.reduce((acc, item) => acc + item.preco * item.quantidade, 0);
            localStorage.setItem("totalCompra", total.toFixed(2));
            window.location.href = "../html/identificacao.html";
        });

        renderCarrinho();

    </script>
</body>

</html>