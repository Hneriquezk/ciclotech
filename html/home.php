<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/flash.php';
require_login();
$msg = flash_get();

// Verifica se o usuário está logado como cliente normal
$logado = isset($_SESSION['usuario_id']);

// Obtém informações do usuário logado
if ($logado) {
    $usuario_id = $_SESSION['usuario_id'];
    $usuario_nome = $_SESSION['usuario_nome'] ?? '';
    $usuario_email = $_SESSION['usuario_email'] ?? '';
    
    // Buscar foto do usuário do banco de dados
    $foto_perfil = '';
    $conn = new mysqli("localhost", "root", "", "ciclotech");
    if ($conn->connect_error) {
        die("Erro na conexão: " . $conn->connect_error);
    }
    
    $sql = "SELECT foto_perfil FROM clientes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $foto_perfil = $row['foto_perfil'] ?? '';
    }
    $stmt->close();
    $conn->close();
}
?> 
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Ciclotech - Home</title>
  <link rel="stylesheet" href="../css/homepage.css">
  <link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Anton&family=Poppins&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="lado-esquerdo">
            <a href="logout.php" class="sair">
                <i class="fas fa-sign-out-alt"></i> Sair
            </a>
        </div>

        <div class="logo">
            <img src="../img/logo.png" alt="Ciclotech">
        </div>

        <div class="carrinho">
    <img src="../img/carrinho.png" alt="Carrinho" style="width: 25px; height: 25px; margin-right: 20px;">
    <img src="../img/Search.png" style="width: 25px; height: 25px; margin-right: 20px;">
    
    <?php if ($logado): ?>
        <a href="../html/usuario.php">
            <?php if (!empty($foto_perfil) && file_exists('../uploads/' . $foto_perfil)): ?>
                <img src="../uploads/<?php echo htmlspecialchars($foto_perfil); ?>" 
                     alt="Foto de Perfil" 
                     style="width: 30px; height: 30px; margin-right: 20px; border-radius: 50%; object-fit: cover; border: 2px solid #fff;">
            <?php else: ?>
                <img src="../img/user.png" 
                     alt="Foto Padrão" 
                     style="width: 30px; height: 30px; margin-right: 20px; border-radius: 50%; object-fit: cover; border: 2px solid #fff;">
            <?php endif; ?>
        </a>
    <?php else: ?>
        <a href="../html/login.php">
            <img src="../img/user.png" 
                 alt="Login" 
                 style="width: 30px; height: 30px; margin-right: 20px; border-radius: 50%; object-fit: cover; border: 2px solid #fff;">
        </a>
    <?php endif; ?>
    
    <a href="contato.html" class="botaoContato" title="Fale Conosco">
        <img id="ImgContato" src="../img/contato.png" alt="Contato">
    </a>
</div>

    </header>
    <!-- ================= BARRA DE PESQUISA ================= -->
    <div class="barra-de-buscar-container">
        <div class="barra-de-buscar">
            <input id="input-busca" type="text" placeholder="O que você está buscando?">
            <button id="botao-busca">
                <img src="../img/Search.png" alt="Buscar">
            </button>
        </div>

        <!-- ================= RESULTADOS DA PESQUISA ================= -->
        <h1 id="titulo-pesquisa" style="display:none;">Resultados da Pesquisa</h1>
        <div id="resultado-pesquisa" class="container">
        </div>
    </div>
    <br>
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

    <a href="evoworldcup.html"><img src="../img/banner-home.png" alt="Promoção Bike Shimano"
            style="width: 100%; height: 750px;"></a>
    <br><br>

    <h1>ITENS EM DESTAQUE</h1>

    <div class="container">
         <div class="card">
                <a href="kitsram.html">
                <img src="../img/Kit Sram Sport.png" alt="KIT SRAM SPORT">
                <h2>KIT SRAM SPORT</h2>
                <div class="preco-antigo">De: R$3.049,80</div>
                <div class="avista">À Vista</div>
                <div class="preco-atual">R$2.658,10</div>
                <div class="desconto">No Pix já com 5% de desconto</div>
                <hr><br>
                <div class="parcelas">R$2.798,00 ou 12X de <span>R$233,17</span> sem juros</div>
                <br>
                  <button class="botao-comprar" onclick="mostrarAlertaLogin()"><a  href="kitsram.html">COMPRAR</a></button>
                </a>
        </div>

        <div class="card">
                <a href="kitmagura.html">
                <img src="../img/Kit Magura.png" alt="KIT MAGURA">
                <h2>KIT MAGURA</h2>
                <div class="avista">À Vista</div>
                <div class="preco-atual">R$4.463,10</div>
                <div class="desconto">No Pix já com 5% de desconto</div>
                <hr><br>
                <div class="parcelas">R$4.698,00 ou 12X de <span>R$391,50</span> sem juros</div>
                <br>
                  <button class="botao-comprar" onclick="mostrarAlertaLogin()"><a  href="kitmagura.html">COMPRAR</a></button>
                </a>
        </div>

        <div class="card">
                <a href="kitcompreeganhe.html">
                <img src="../img/kit_compreganhe.png" alt="KIT COMPRE E GANHE">
                <h2>KIT COMPRE E GANHE</h2>
                <div class="avista">À Vista</div>
                <div class="preco-atual">R$369,53</div>
                <div class="desconto">No Pix já com 2.5% de desconto</div>
                <hr><br>
                <div class="parcelas">R$379,00 ou 12X de <span>R$31,58</span> sem juros</div>
                <br>
                  <button class="botao-comprar" onclick="mostrarAlertaLogin()"><a  href="kitcompreeganhe.html">COMPRAR</a></button>
                </a>
        </div>

        <div class="card">
                <a href="kitupgrade.html">
                <img src="../img/kit de upgrade sram.png" alt="KIT de Upgrade Sram Original GX Eagle AXS">
                <h2>KIT de Upgrade Sram Original GX Eagle AXS</h2>
                <div class="avista">À Vista</div>
                <div class="preco-atual">R$4.939,05</div>
                <div class="desconto">No Pix já com 5% de desconto</div>
                <hr><br>
                <div class="parcelas">R$5.199,00 ou 12X de <span>R$433,25</span> sem juros</div>
                <br>
                  <button class="botao-comprar" onclick="mostrarAlertaLogin()"><a  href="kitupgrade.html">COMPRAR</a></button>
                </a>
        </div>
    </div>
    <br>
    <div class="container">
        <div class="card">
                <a href="scottscale.html">
                <img src="../img/scott scale.png" alt="SCOTT Scale 980 Dark Grey">
                <h2>SCOTT Scale 980 Dark Grey</h2>
                <div class="preco-antigo">De: R$8.000,00</div>
                <div class="avista">À Vista</div>
                <div class="preco-atual">R$6.640,50</div>
                <div class="desconto">No Pix já com 5% de desconto</div>
                <hr><br>
                <div class="parcelas">R$6.990,00 ou 12X de <span>R$582,50</span> sem juros</div>
                <br>
                  <button class="botao-comprar" onclick="mostrarAlertaLogin()"><a  href="scottscale.html">COMPRAR</a></button>
                </a>
        </div>

        <div class="card">
                <a href="scottazul.html">
                <img src="../img/scott azul.png" alt="SCOTT Spark RC Azul Metálico">
                <h2>SCOTT Spark RC Azul Metálico</h2>
                <div class="preco-antigo">De: R$39.999,00</div>
                <div class="avista">À Vista</div>
                <div class="preco-atual">R$33.249,05</div>
                <div class="desconto">No Pix já com 5% de desconto</div>
                <hr><br>
                <div class="parcelas">R$34.999,00 ou 12X de <span>R$2.916,58</span> sem juros</div>
                <br>
                  <button class="botao-comprar" onclick="mostrarAlertaLogin()"><a  href="scottazul.html">COMPRAR</a></button>
                </a>
        </div>

        <div class="card">
                <a href="scottfoil.html">
                <img src="../img/scott foil rc10.png" alt="Bicicleta Scott Foil RC 10 Preto Carbono Aparente">
                <h2>Bicicleta Scott Foil RC 10 Preto Carbono Aparente</h2>
                <div class="avista">À Vista</div>
                <div class="preco-atual">R$64.599,91</div>
                <div class="desconto">No Pix já com 5% de desconto</div>
                <hr><br>
                <div class="parcelas">R$67.999,90 ou 12X de <span>R$5.666,66</span> sem juros</div>
                <br>
                  <button class="botao-comprar" onclick="mostrarAlertaLogin()"><a  href="scottfoil.html">COMPRAR</a></button>
                </a>
        </div>

        <div class="card">
                <a href="scottrc.html">
                <img src="../img/scott foil rc 30.png" alt="Bicicleta Scott Foil RC 30 Ice Grey">
                <h2>Bicicleta Scott Foil RC 30 Ice Grey</h2>
                <div class="avista">À Vista</div>
                <div class="preco-atual">R$47.499,91</div>
                <div class="desconto">No Pix já com 5% de desconto</div>
                <hr><br>
                <div class="parcelas">R$49.999,90 ou 12X de <span>R$4.166,66</span> sem juros</div>
                <br>
                  <button class="botao-comprar" onclick="mostrarAlertaLogin()"><a  href="scottrc.html">COMPRAR</a></button>
                </a>
        </div>
    </div>

    <br>

    <h1>QUE TIPO DE CICLISTA É VOCÊ?</h1>

    <p style="text-align: center;">"Clicotech: Conectando você ao seu estilo de pedal"</p>
    <br>
    <div class="modalidade">
        <a href="estrada.html">
            <div class="card2">
                <img src="../img/estrada.jpg" alt="Estrada">
                <span>ESTRADA</span>
            </div>
        </a>

        <a href="montanha.html">
            <div class="card2">
                <img src="../img/escalada.jpg" alt="Escalada">
                <span>MONTANHA</span>
            </div>
        </a>

        <a href="trilha.html">
            <div class="card2">
                <img src="../img/trilha2.jpg" alt="Trilha">
                <span>TRILHA</span>
            </div>
        </a>

        <a href="cidade.html">
            <div class="card2">
                <img src="../img/cidade.jpg" alt="Cidade">
                <span>CIDADE</span>
            </div>
        </a>
    </div>
    <br>

    <h1>FIQUE POR DENTRO DE NOSSAS PROMOÇÕES</h1>
    <br>

    <div class="container">
        <div class="card">
            <a href="capaceteabus.html">
                <img src="../img/capacete abus.png" alt="Capacete Abus Airbreaker Azul">
                <h2>Capacete Abus Airbreaker Azul</h2>
                <div class="preco-antigo">De: R$2.549,00</div>
                <div class="avista">À Vista</div>
                <div class="preco-atual">R$2.374,05</div>
                <div class="desconto">No Pix já com 5% de desconto</div>
                <hr><br>
                <div class="parcelas">R$2.499.00 ou 12X de <span>R$208,25</span> sem juros</div>
                <br>
                  <button class="botao-comprar" onclick="mostrarAlertaLogin()"><a  href="capaceteabus.html">COMPRAR</a></button>
            </a>
            <br>
        </div>

        <div class="card">
            <a href="sapatilhabranca.html">
                <img src="../img/sapatilha shimano branco.png" alt="Sapatilha Shimano SH-RC300 Speed Branca">
                <h2>Sapatilha Shimano SH-RC300 Speed Branca</h2>
                <div class="preco-antigo">De: R$1.000,00</div>
                <div class="avista">À Vista</div>
                <div class="preco-atual">R$806,55</div>
                <div class="desconto">No Pix já com 5% de desconto</div>
                <hr><br>
                <div class="parcelas">R$849,00 ou 12X de <span>R$70,75</span> sem juros</div>
                <br>
                  <button class="botao-comprar" onclick="mostrarAlertaLogin()"><a  href="sapatilhabranca.html">COMPRAR</a></button>
                <br>
            </a>
        </div>

        <div class="card">
            <a href="sapatilhapreta.html">
                <img src="../img/sapatilha shimano preto.png" alt="Sapatilha Shimano SH-XC300 MTB Preta">
                <h2>Sapatilha Shimano SH-XC300 MTB Preta</h2>
                <div class="preco-antigo">De: R$1.000,00</div>
                <div class="avista">À Vista</div>
                <div class="preco-atual">R$790,28</div>
                <div class="desconto">No Pix já com 10% de desconto</div>
                <hr><br>
                <div class="parcelas">R$899,00 ou 12X de <span>R$74,92</span> sem juros</div>
                <br>
                  <button class="botao-comprar" onclick="mostrarAlertaLogin()"><a  href="sapatilhapreta.html">COMPRAR</a></button>
                <br>
            </a>
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
// ======================= CARRINHO LATERAL =======================

// Seleciona os elementos principais do carrinho
const carrinho = document.getElementById("carrinho-lateral");
const fecharCarrinho = document.getElementById("fechar-carrinho");
const carrinhoItens = document.getElementById("carrinho-itens");
const carrinhoTotal = document.getElementById("carrinho-total");

// --- Abre o carrinho ao clicar no ícone ---
document.querySelector(".carrinho img[src='../img/carrinho.png']").addEventListener("click", () => {
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

// ======================= PESQUISA DE PRODUTOS =======================

const inputBusca = document.getElementById("input-busca");
const botaoBusca = document.getElementById("botao-busca");
const resultadoContainer = document.getElementById("resultado-pesquisa");
const tituloPesquisa = document.getElementById("titulo-pesquisa");
const todosCards = document.querySelectorAll(".container .card");
document.querySelector(".container").style.backgroundColor = "#000";

function pesquisarProduto() {
    const termo = inputBusca.value.toLowerCase().trim();
    resultadoContainer.innerHTML = "";

    if (termo === "") {
        tituloPesquisa.style.display = "none";
        resultadoContainer.style.display = "none";
        return;
    }

    let encontrados = 0;

    todosCards.forEach(card => {
        card.style.backgroundColor = "#fff";
        card.querySelector("h2").style.color = "#000";

        const titulo = card.querySelector("h2").innerText.toLowerCase();
        const preco = card.querySelector(".preco-atual")?.innerText.toLowerCase() || "";

        if (titulo.includes(termo) || preco.includes(termo)) {
            resultadoContainer.appendChild(card.cloneNode(true));
            encontrados++;
        }
    });

    tituloPesquisa.style.display = "block";
    resultadoContainer.style.display = "flex";

    if (encontrados === 0) {
        resultadoContainer.innerHTML = "<p>Nenhum produto encontrado.</p>";
    }
}

inputBusca.addEventListener("input", pesquisarProduto);
botaoBusca.addEventListener("click", pesquisarProduto);

// ======================= OVERLAY DE PESQUISA =======================

const iconeBusca = document.querySelector('.carrinho img[src="../img/Search.png"]');
const overlayPesquisa = document.createElement('div');

overlayPesquisa.id = "overlay-pesquisa";
overlayPesquisa.innerHTML = `
  <div class="overlay-conteudo">
    <div class="overlay-busca">
      <img src="../img/Search-preto.png" alt="Buscar">
      <input type="text" id="input-overlay" placeholder="Pesquisar">
      <span id="btn-cancelar">Cancelar</span>
    </div>

    <div class="categorias">
      <h2>Explorar por Categoria</h2>
      <div class="categorias-grid">
        <a href="estrada.html" class="categorias-item">
          <img src="../img/estrada.jpg" alt="Estrada">
          <span>Bikes de Estrada</span>
        </a>
        <a href="montanha.html" class="categorias-item">
          <img src="../img/escalada.jpg" alt="Montanha">
          <span>Bikes de Montanha</span>
        </a>
        <a href="trilha.html" class="categorias-item">
          <img src="../img/trilha2.jpg" alt="Trilha">
          <span>Bikes de Trilha</span>
        </a>
        <a href="cidade.html" class="categorias-item">
          <img src="../img/cidade.jpg" alt="Cidade">
          <span>Bikes Urbanas</span>
        </a>
      </div>
    </div>

    <div id="resultado-pesquisa"></div>
  </div>
`;

document.body.appendChild(overlayPesquisa);

const inputOverlay = overlayPesquisa.querySelector('#input-overlay');
const btnCancelar = overlayPesquisa.querySelector('#btn-cancelar');
const resultadoOverlay = overlayPesquisa.querySelector('#resultado-pesquisa');
const containerCategorias = overlayPesquisa.querySelector('.categorias');

// === Abrir overlay ===
iconeBusca.addEventListener('click', () => {
    overlayPesquisa.style.display = 'block';
    inputOverlay.focus();
});

// === Fechar overlay ===
btnCancelar.addEventListener('click', () => {
    overlayPesquisa.style.display = 'none';
    inputOverlay.value = '';
    resultadoOverlay.innerHTML = '';
    resultadoOverlay.style.display = 'none';
    containerCategorias.style.display = 'block';
});

// === Função de pesquisa no overlay ===
function pesquisarNoOverlay() {
    const termo = inputOverlay.value.toLowerCase().trim();
    resultadoOverlay.innerHTML = "";

    if (termo === "") {
        resultadoOverlay.style.display = "none";
        containerCategorias.style.display = "block"; // Mostra categorias quando input vazio
        return;
    }

    containerCategorias.style.display = "none"; // Esconde categorias quando há pesquisa

    let encontrados = 0;

    document.querySelectorAll(".container .card").forEach(card => {
        const titulo = card.querySelector("h2").innerText.toLowerCase();
        const preco = card.querySelector(".preco-atual")?.innerText.toLowerCase() || "";

        if (titulo.includes(termo) || preco.includes(termo)) {
            resultadoOverlay.appendChild(card.cloneNode(true));
            encontrados++;
        }
    });

    resultadoOverlay.style.display = "flex";

    if (encontrados === 0) {
        resultadoOverlay.innerHTML = "<p>Nenhum produto encontrado.</p>";
    }
}

// === Eventos ===
inputOverlay.addEventListener("input", pesquisarNoOverlay);

// Alerta
function mostrarAlertaLogin() {
  document.getElementById("alerta-login").style.display = "flex";
}
document.getElementById("btn-login-alerta").addEventListener("click", () => {
  window.location.href = "login.php";
});
document.getElementById("btn-fechar-alerta").addEventListener("click", () => {
  document.getElementById("alerta-login").style.display = "none";
});
    </script>
</body>

</html>