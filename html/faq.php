<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ciclotech - Perguntas Frequentes</title>
    <link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Poppins&display=swap" rel="stylesheet">
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
  background: #000;
  color: #fff;
  line-height: 1.6;
}

header {
  background: #000;
  display: flex;
  padding: 15px 40px;
  width: 100%;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid #333;
}

.lado-esquerdo {
  display: flex;
  gap: 10px;
  width: 25%;
}

header .lado-esquerdo a {
  text-decoration: none;
  color: #fff;
  font-weight: bold;
  font-size: 14px;
  font-family: 'Poppins', sans-serif;
  transition: color 0.3s;
}

header .lado-esquerdo a:hover {
  color: #d80000;
}

.logo {
  height: 70px;
  display: flex;
}

.logo img {
  height: 100%;
}

/* === BOTÃO FIXO DE CONTATO === */
.botaoContato {
  position: fixed;
  bottom: 20px;
  left: 20px;
  background-color: #e60000;
  border-radius: 50%;
  width: 60px;
  height: 60px;
  display: flex;
  justify-content: center;
  align-items: center;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
  z-index: 1000;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.botaoContato:hover {
  transform: scale(1.1);
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.4);
}

#ImgContato {
  width: 30px;
  height: 30px;
  filter: brightness(0) invert(1);
}

.carrinho {
  height: 40px;
  cursor: pointer;
  display: flex;
  width: 25%;
  justify-content: end;
  align-items: center;
}

/* ================= MAIN CONTENT ================= */
main {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.page-header {
  text-align: center;
  margin: 40px 0;
}

.page-title {
  font-size: 36px;
  color: #fff;
  margin-bottom: 15px;
  font-weight: 600;
}

.page-subtitle {
  font-size: 18px;
  color: #ddd;
  max-width: 600px;
  margin: 0 auto;
}

/* ================= FAQ CONTAINER ================= */
.faq-container {
  display: flex;
  gap: 30px;
  margin-bottom: 40px;
}

.faq-content {
  flex: 2;
}

.sidebar {
  flex: 1;
}

/* ================= SEARCH SECTION ================= */
.search-section {
  background: #1a1a1a;
  border-radius: 10px;
  padding: 30px;
  margin-bottom: 30px;
  text-align: center;
}

.search-title {
  color: #fff;
  margin-bottom: 20px;
  font-size: 24px;
}

.search-form {
  display: flex;
  gap: 10px;
  max-width: 500px;
  margin: 0 auto;
}

.search-input {
  flex: 1;
  padding: 14px;
  border: 1px solid #444;
  border-radius: 8px;
  background: #2a2a2a;
  color: #fff;
  font-size: 16px;
}

.search-input:focus {
  outline: none;
  border-color: #d80000;
}

.search-button {
  background: #d80000;
  color: white;
  border: none;
  padding: 14px 20px;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.3s;
}

.search-button:hover {
  background: #b30000;
}

/* ================= FAQ CATEGORIES ================= */
.faq-categories {
  display: flex;
  gap: 15px;
  margin-bottom: 30px;
  flex-wrap: wrap;
}

.category-btn {
  background: #2a2a2a;
  color: #fff;
  border: none;
  padding: 12px 20px;
  border-radius: 25px;
  cursor: pointer;
  transition: all 0.3s;
  font-weight: 500;
}

.category-btn.active, .category-btn:hover {
  background: #d80000;
  transform: translateY(-2px);
}

/* ================= FAQ ACCORDION ================= */
.faq-accordion {
  background: #1a1a1a;
  border-radius: 10px;
  padding: 30px;
}

.faq-section {
  margin-bottom: 30px;
}

.faq-section:last-child {
  margin-bottom: 0;
}

.section-title {
  color: #d80000;
  font-size: 22px;
  margin-bottom: 20px;
  padding-bottom: 10px;
  border-bottom: 2px solid #333;
  display: flex;
  align-items: center;
  gap: 10px;
}

.faq-item {
  margin-bottom: 15px;
  border: 1px solid #333;
  border-radius: 8px;
  overflow: hidden;
  transition: all 0.3s;
}

.faq-item:hover {
  border-color: #444;
}

.faq-question {
  background: #2a2a2a;
  padding: 20px;
  cursor: pointer;
  display: flex;
  justify-content: space-between;
  align-items: center;
  transition: background 0.3s;
}

.faq-question:hover {
  background: #333;
}

.faq-question h3 {
  font-size: 16px;
  color: #fff;
  font-weight: 600;
  margin: 0;
}

.faq-icon {
  color: #d80000;
  transition: transform 0.3s;
}

.faq-item.active .faq-icon {
  transform: rotate(180deg);
}

.faq-answer {
  background: #2a2a2a;
  padding: 0;
  max-height: 0;
  overflow: hidden;
  transition: all 0.3s ease;
}

.faq-item.active .faq-answer {
  padding: 20px;
  max-height: 500px;
  border-top: 1px solid #333;
}

.faq-answer p {
  color: #ddd;
  margin-bottom: 15px;
  line-height: 1.6;
}

.faq-answer p:last-child {
  margin-bottom: 0;
}

.faq-answer ul {
  color: #ddd;
  padding-left: 20px;
  margin-bottom: 15px;
}

.faq-answer li {
  margin-bottom: 8px;
}

/* ================= SIDEBAR ================= */
.sidebar-card {
  background: #1a1a1a;
  border-radius: 10px;
  padding: 25px;
  margin-bottom: 25px;
}

.sidebar-title {
  color: #fff;
  margin-bottom: 20px;
  font-size: 18px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.sidebar-title i {
  color: #d80000;
}

.contact-info {
  color: #ddd;
}

.contact-item {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  margin-bottom: 15px;
}

.contact-item:last-child {
  margin-bottom: 0;
}

.contact-icon {
  color: #d80000;
  margin-top: 2px;
  flex-shrink: 0;
}

.contact-text h4 {
  color: #fff;
  margin-bottom: 5px;
  font-size: 14px;
}

.contact-text p {
  color: #ddd;
  font-size: 14px;
  line-height: 1.4;
}

.quick-links {
  list-style: none;
}

.quick-links li {
  margin-bottom: 10px;
}

.quick-links a {
  color: #ddd;
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: color 0.3s;
  font-size: 14px;
}

.quick-links a:hover {
  color: #d80000;
}

.quick-links i {
  color: #d80000;
  width: 16px;
}

/* ================= SUPPORT BANNER ================= */
.support-banner {
  background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
  border-radius: 10px;
  padding: 40px;
  text-align: center;
  margin-top: 40px;
  border: 1px solid #333;
}

.banner-title {
  color: #fff;
  font-size: 28px;
  margin-bottom: 15px;
}

.banner-text {
  color: #ddd;
  margin-bottom: 25px;
  font-size: 16px;
}

.banner-actions {
  display: flex;
  gap: 15px;
  justify-content: center;
  flex-wrap: wrap;
}

.btn-primary {
  background-color: #d80000;
  color: white;
  padding: 14px 25px;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s, transform 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  text-decoration: none;
}

.btn-primary:hover {
  background-color: #b30000;
  transform: translateY(-2px);
}

.btn-secondary {
  background-color: #444;
  color: white;
  padding: 14px 25px;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s, transform 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  text-decoration: none;
}

.btn-secondary:hover {
  background-color: #555;
  transform: translateY(-2px);
}

/* ================= FOOTER ================= */
footer {
  margin-top: 60px;
}

.footer-top {
  background: #d80000;
  color: #fff;
  display: flex;
  justify-content: space-around;
  align-items: center;
  padding: 15px 0;
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
  transition: color 0.3s;
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
  transition: transform 0.3s;
}

.footer-social img:hover {
  transform: translateY(-3px);
}

.footer-payment img {
  width: 40px;
  margin: 5px;
}

.footer-payment {
  text-align: center;
}

/* ================= RESPONSIVIDADE ================= */
@media (max-width: 992px) {
  .faq-container {
    flex-direction: column;
  }
  
  .footer-main {
    grid-template-columns: 1fr 1fr;
    padding: 30px 20px;
  }
  
  .banner-actions {
    flex-direction: column;
  }
  
  .banner-actions a,
  .banner-actions button {
    width: 100%;
  }
}

@media (max-width: 768px) {
  .search-form {
    flex-direction: column;
  }
  
  .faq-categories {
    justify-content: center;
  }
  
  .footer-main {
    grid-template-columns: 1fr;
  }
  
  .footer-top {
    flex-direction: column;
    gap: 10px;
  }
  
  .page-title {
    font-size: 28px;
  }
}
</style>
<body>
    <header>
        <div class="lado-esquerdo">
            <a href="logout.php">SAIR</a>
            <a href="home.php">HOME</a>
        </div>

        <div class="logo">
            <img src="../img/logo.png" alt="Ciclotech">
        </div>

        <div class="carrinho">
            <a href="contato.html" class="botaoContato" title="Fale Conosco">
                <img id="ImgContato" src="../img/contato.png" alt="Contato">
            </a>
        </div>
    </header>
    
    <main>
        <div class="page-header">
            <h1 class="page-title">Perguntas Frequentes</h1>
            <p class="page-subtitle">Encontre respostas para as dúvidas mais comuns sobre nossos produtos, entregas, pagamentos e muito mais.</p>
        </div>
        
        <div class="search-section">
            <h2 class="search-title">Como podemos ajudar?</h2>
            <form class="search-form">
                <input type="text" class="search-input" placeholder="Digite sua dúvida...">
                <button type="submit" class="search-button">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
        
        <div class="faq-container">
            <div class="faq-content">
                <div class="faq-categories">
                    <button class="category-btn active" data-category="all">Todas</button>
                    <button class="category-btn" data-category="pedidos">Pedidos</button>
                    <button class="category-btn" data-category="entregas">Entregas</button>
                    <button class="category-btn" data-category="pagamentos">Pagamentos</button>
                    <button class="category-btn" data-category="produtos">Produtos</button>
                    <button class="category-btn" data-category="devolucoes">Trocas & Devoluções</button>
                </div>
                
                <div class="faq-accordion">
                    <!-- PEDIDOS -->
                    <div class="faq-section" data-category="pedidos">
                        <h2 class="section-title"><i class="fas fa-shopping-cart"></i> Pedidos</h2>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <h3>Como faço para acompanhar meu pedido?</h3>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Você pode acompanhar seu pedido de duas formas:</p>
                                <ul>
                                    <li>Acesse a página de <a href="rastreio.php" style="color: #d80000;">Rastreamento</a> e insira o número do seu pedido</li>
                                    <li>Acesse sua conta no site e visualize o histórico de pedidos</li>
                                    <li>Você também receberá e-mails com atualizações do status do pedido</li>
                                </ul>
                                <p>O número do pedido é enviado para seu e-mail após a confirmação do pagamento.</p>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <h3>Posso alterar ou cancelar meu pedido após a compra?</h3>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Alterações ou cancelamentos são possíveis apenas se o pedido ainda não tiver sido enviado para a transportadora.</p>
                                <p>Para solicitar alterações ou cancelamento:</p>
                                <ul>
                                    <li>Entre em contato com nosso atendimento pelo telefone (12) 9 9729-1076</li>
                                    <li>Envie um e-mail para ciclotechi.atendimento@gmail.com</li>
                                    <li>Utilize o chat online em nosso site</li>
                                </ul>
                                <p>Pedidos já enviados não podem ser cancelados, mas você pode solicitar a devolução após o recebimento.</p>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <h3>Quanto tempo leva para o pedido ser processado?</h3>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>O processamento do pedido geralmente leva de 1 a 2 dias úteis após a confirmação do pagamento.</p>
                                <p>Em períodos de promoção ou feriados, este prazo pode ser estendido para até 3 dias úteis.</p>
                                <p>Você receberá uma notificação por e-mail quando seu pedido for enviado.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- ENTREGAS -->
                    <div class="faq-section" data-category="entregas">
                        <h2 class="section-title"><i class="fas fa-truck"></i> Entregas</h2>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <h3>Quais são os prazos de entrega?</h3>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Os prazos de entrega variam conforme a região:</p>
                                <ul>
                                    <li><strong>Capital e Região Metropolitana:</strong> 3 a 5 dias úteis</li>
                                    <li><strong>Interior:</strong> 5 a 8 dias úteis</li>
                                    <li><strong>Outros Estados:</strong> 7 a 12 dias úteis</li>
                                    <li><strong>Regiões Norte/Nordeste:</strong> 10 a 15 dias úteis</li>
                                </ul>
                                <p>Os prazos começam a contar a partir da data de postagem, que você acompanha pelo rastreamento.</p>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <h3>Vocês entregam para todo o Brasil?</h3>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Sim, entregamos para todo o território nacional através de nossas transportadoras parceiras.</p>
                                <p>Para algumas localidades de difícil acesso, pode haver restrições ou prazos diferenciados. Nestes casos, nossa equipe entrará em contato para combinar a melhor forma de entrega.</p>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <h3>É possível agendar a entrega?</h3>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Sim, para algumas regiões é possível agendar a entrega. Quando o pedido estiver em rota de entrega, a transportadora entrará em contato para combinar o melhor horário.</p>
                                <p>Se você preferir agendar antecipadamente, entre em contato conosco após a confirmação do pedido.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- PAGAMENTOS -->
                    <div class="faq-section" data-category="pagamentos">
                        <h2 class="section-title"><i class="fas fa-credit-card"></i> Pagamentos</h2>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <h3>Quais formas de pagamento são aceitas?</h3>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Aceitamos as seguintes formas de pagamento:</p>
                                <ul>
                                    <li><strong>Cartão de Crédito:</strong> Visa, Mastercard, Elo, American Express (em até 12x)</li>
                                    <li><strong>PIX:</strong> Com 10% de desconto</li>
                                    <li><strong>Boleto Bancário:</strong> À vista com 5% de desconto</li>
                                    <li><strong>Cartão de Débito:</strong> Visa Electron, Maestro</li>
                                    <li><strong>PayPal</strong></li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <h3>O pagamento no PIX é realmente mais barato?</h3>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Sim! Oferecemos 10% de desconto para pagamentos via PIX. O desconto é aplicado automaticamente no fechamento do pedido.</p>
                                <p>Após a confirmação do pagamento PIX, o pedido é processado imediatamente, garantindo agilidade na entrega.</p>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <h3>Como funciona o parcelamento no cartão?</h3>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Oferecemos parcelamento em até 12x sem juros para compras acima de R$ 300,00.</p>
                                <p>Para valores menores, o parcelamento está disponível em até 6x sem juros.</p>
                                <p>As parcelas são calculadas automaticamente no carrinho de compras antes do fechamento do pedido.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- PRODUTOS -->
                    <div class="faq-section" data-category="produtos">
                        <h2 class="section-title"><i class="fas fa-bicycle"></i> Produtos</h2>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <h3>Os produtos possuem garantia?</h3>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Sim, todos os nossos produtos possuem garantia do fabricante. O prazo de garantia varia conforme o produto:</p>
                                <ul>
                                    <li><strong>Bicicletas completas:</strong> 1 ano</li>
                                    <li><strong>Componentes:</strong> 6 meses a 1 ano</li>
                                    <li><strong>Vestuário e acessórios:</strong> 3 meses</li>
                                </ul>
                                <p>A garantia cobre defeitos de fabricação. Em caso de problemas, entre em contato com nosso suporte técnico.</p>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <h3>Como escolher o tamanho correto do produto?</h3>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Cada produto em nossa loja possui uma tabela de medidas detalhada na página do produto.</p>
                                <p>Para bicicletas, recomendamos:</p>
                                <ul>
                                    <li>Medir a altura do ciclista</li>
                                    <li>Considerar o tipo de uso (estrada, montanha, urbana)</li>
                                    <li>Verificar o tamanho do quadro na descrição do produto</li>
                                </ul>
                                <p>Em caso de dúvidas, nossa equipe de especialistas pode ajudar na escolha pelo chat ou telefone.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- TROCAS & DEVOLUÇÕES -->
                    <div class="faq-section" data-category="devolucoes">
                        <h2 class="section-title"><i class="fas fa-exchange-alt"></i> Trocas & Devoluções</h2>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <h3>Qual é o prazo para trocas e devoluções?</h3>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>O prazo para solicitar trocas ou devoluções é de 30 dias corridos a partir da data de recebimento do produto.</p>
                                <p>Para exercer esse direito, o produto deve estar:</p>
                                <ul>
                                    <li>Na embalagem original</li>
                                    <li>Com todas as etiquetas</li>
                                    <li>Sem indícios de uso</li>
                                    <li>Acompanhado da nota fiscal</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <h3>Como funciona o processo de troca?</h3>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>O processo de troca é simples:</p>
                                <ol>
                                    <li>Entre em contato conosco informando o motivo da troca</li>
                                    <li>Nossa equipe enviará um código de postagem</li>
                                    <li>Encaminhe o produto para nosso centro de distribuição</li>
                                    <li>Após a análise, enviaremos o novo produto ou efetuaremos o estorno</li>
                                </ol>
                                <p>O prazo para processamento da troca é de até 5 dias úteis após o recebimento do produto em nosso centro.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- SIDEBAR -->
            <div class="sidebar">
                <div class="sidebar-card">
                    <h3 class="sidebar-title"><i class="fas fa-headset"></i> Precisa de Ajuda?</h3>
                    <div class="contact-info">
                        <div class="contact-item">
                            <i class="fas fa-phone contact-icon"></i>
                            <div class="contact-text">
                                <h4>Telefone</h4>
                                <p>(12) 9 9729-1076<br>Segunda a Sexta, 9h às 18h</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-envelope contact-icon"></i>
                            <div class="contact-text">
                                <h4>E-mail</h4>
                                <p>ciclotech.atendimento@gmail.com</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-comments contact-icon"></i>
                            <div class="contact-text">
                                <h4>Chat Online</h4>
                                <p>Disponível durante o horário comercial</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="sidebar-card">
                    <h3 class="sidebar-title"><i class="fas fa-link"></i> Links Rápidos</h3>
                    <ul class="quick-links">
                        <li><a href="rastreio.php"><i class="fas fa-truck"></i> Rastrear Pedido</a></li>
                        <li><a href="contato.html"><i class="fas fa-envelope"></i> Fale Conosco</a></li>
                        <li><a href="#"><i class="fas fa-file-alt"></i> Política de Trocas</a></li>
                        <li><a href="#"><i class="fas fa-shield-alt"></i> Política de Privacidade</a></li>
                        <li><a href="#"><i class="fas fa-truck"></i> Política de Entregas</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- SUPPORT BANNER -->
        <div class="support-banner">
            <h3 class="banner-title">Não encontrou o que procurava?</h3>
            <p class="banner-text">Nossa equipe de atendimento está pronta para ajudar você!</p>
            <div class="banner-actions">
                <a href="https://mail.google.com/mail/u/0/#inbox" class="btn-primary">
                    <i class="fas fa-envelope"></i> Entrar em Contato
                </a>
            </div>
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
    // ======================= FAQ ACCORDION - CORRIGIDO =======================
    document.addEventListener('DOMContentLoaded', function() {
        // Accordion functionality
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', () => {
                const faqItem = question.parentElement;
                const isActive = faqItem.classList.contains('active');
                
                // Fechar todos os itens primeiro
                document.querySelectorAll('.faq-item').forEach(item => {
                    item.classList.remove('active');
                });
                
                // Se o item clicado não estava ativo, abrir ele
                if (!isActive) {
                    faqItem.classList.add('active');
                }
            });
        });

        // ======================= CATEGORY FILTER - CORRIGIDO =======================
        document.querySelectorAll('.category-btn').forEach(button => {
            button.addEventListener('click', () => {
                const category = button.getAttribute('data-category');
                
                // Atualizar botões ativos
                document.querySelectorAll('.category-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                button.classList.add('active');
                
                // Filtrar FAQs - CORREÇÃO: Mostrar todas as seções e itens quando for "all"
                document.querySelectorAll('.faq-section').forEach(section => {
                    if (category === 'all') {
                        section.style.display = 'block';
                        // Mostrar todos os itens dentro da seção
                        section.querySelectorAll('.faq-item').forEach(item => {
                            item.style.display = 'block';
                        });
                    } else if (section.getAttribute('data-category') === category) {
                        section.style.display = 'block';
                        // Mostrar todos os itens dentro da seção
                        section.querySelectorAll('.faq-item').forEach(item => {
                            item.style.display = 'block';
                        });
                    } else {
                        section.style.display = 'none';
                    }
                });
            });
        });

        // ======================= SEARCH FUNCTIONALITY - CORRIGIDO =======================
        document.querySelector('.search-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const searchTerm = this.querySelector('.search-input').value.toLowerCase().trim();
            
            // Resetar filtro de categorias quando pesquisar
            document.querySelectorAll('.category-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector('.category-btn[data-category="all"]').classList.add('active');
            
            if (!searchTerm) {
                // Se vazio, mostrar todas as categorias
                document.querySelectorAll('.faq-section').forEach(section => {
                    section.style.display = 'block';
                    section.querySelectorAll('.faq-item').forEach(item => {
                        item.style.display = 'block';
                    });
                });
                return;
            }
            
            // Pesquisar nas perguntas e respostas
            let foundResults = false;
            
            document.querySelectorAll('.faq-section').forEach(section => {
                section.style.display = 'block'; // Mostrar a seção
                let sectionHasResults = false;
                
                section.querySelectorAll('.faq-item').forEach(item => {
                    const question = item.querySelector('.faq-question h3').textContent.toLowerCase();
                    const answer = item.querySelector('.faq-answer').textContent.toLowerCase();
                    
                    if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                        item.style.display = 'block';
                        sectionHasResults = true;
                        foundResults = true;
                        
                        // Expandir item encontrado
                        item.classList.add('active');
                    } else {
                        item.style.display = 'none';
                    }
                });
                
                // Se a seção não tem resultados, ocultá-la
                if (!sectionHasResults) {
                    section.style.display = 'none';
                }
            });
            
            // Mostrar mensagem se não encontrar resultados
            if (!foundResults) {
                alert('Nenhum resultado encontrado para: ' + searchTerm);
                // Restaurar visualização normal
                document.querySelectorAll('.faq-section').forEach(section => {
                    section.style.display = 'block';
                    section.querySelectorAll('.faq-item').forEach(item => {
                        item.style.display = 'block';
                    });
                });
            }
        });

        // ======================= RESET FILTER ON PAGE LOAD =======================
        // Garantir que todas as seções estejam visíveis ao carregar a página
        document.querySelectorAll('.faq-section').forEach(section => {
            section.style.display = 'block';
        });
    });
</script>
</body>

</html>