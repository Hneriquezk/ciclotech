<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ciclotech - Rastreio</title>
    <link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/rastreio.css">
    <style>
        /* Estilos para o comprovante de impressão */
        @media print {
            /* Esconder elementos que não devem aparecer no comprovante */
            header, footer, .support-section, .action-buttons,
            .carrinho, .lado-esquerdo {
                display: none !important;
            }
            
            /* Ajustar layout para impressão */
            body {
                font-size: 12pt;
                line-height: 1.4;
                color: #000;
                background: #fff !important;
            }
            
            .tracking-container {
                display: block !important;
                width: 100% !important;
                max-width: none !important;
            }
            
            .tracking-card, .order-info-card {
                width: 100% !important;
                margin-bottom: 20px;
                break-inside: avoid;
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }
            
            /* Otimizar cores para impressão */
            .card-title, .step-title, .info-label {
                color: #000 !important;
            }
            
            .tracking-number {
                color: #000 !important;
                font-weight: bold;
            }
            
            /* Melhorar visualização dos passos */
            .tracking-steps {
                padding: 10px 0;
            }
            
            .tracking-step {
                margin-bottom: 15px;
            }
            
            .step-icon {
                border: 1px solid #000 !important;
            }
            
            .step-icon.completed {
                background-color: #f0f0f0 !important;
                color: #000 !important;
            }
            
            .step-icon.active {
                background-color: #d0d0d0 !important;
                color: #000 !important;
            }
            
            .step-icon.pending {
                background-color: #fff !important;
                color: #888 !important;
            }
            
            /* Ajustar informações do pedido */
            .order-info-card {
                background: #fff !important;
                color: #000 !important;
            }
            
            .order-info-card h2, .order-info-card h3, .order-info-card h4 {
                color: #000 !important;
            }
            
            .order-item {
                border-bottom: 1px solid #ddd;
                padding: 10px 0;
            }
            
            /* Adicionar cabeçalho e rodapé específicos para impressão */
            .print-header, .print-footer {
                display: block !important;
                text-align: center;
                margin: 20px 0;
                padding: 10px;
                border-bottom: 1px solid #000;
            }
            
            .print-footer {
                border-bottom: none;
                border-top: 1px solid #000;
                margin-top: 30px;
                font-size: 10pt;
            }
            
            /* Garantir que as imagens apareçam */
            img {
                max-width: 100% !important;
                height: auto !important;
            }
            
            /* Evitar quebras de página em elementos importantes */
            .tracking-card, .order-info-card {
                page-break-inside: avoid;
            }
        }
        
        /* Estilos para elementos de impressão (ocultos na tela normal) */
        .print-only {
            display: none;
        }
        
        /* Botão de impressão melhorado */
        .btn-secondary {
            cursor: pointer;
        }
    </style>
</head>

<body>
    <!-- Cabeçalho para impressão (oculto na visualização normal) -->
    <div class="print-header print-only">
        <img src="../img/logo_preto.png">
        <p>Emitido em: <span id="print-date"></span></p>
    </div>
    
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
        <!-- ================= PAGE HEADER ================= -->
        <div class="page-header">
            <h1 class="page-title">Acompanhe seu Pedido</h1>
            <p class="page-subtitle">Acompanhe em tempo real o status da sua entrega</p>
        </div>
        
        <div class="tracking-container">
            <!-- ================= TRACKING CARD ================= -->
            <div class="tracking-card">
                <h2 class="card-title"><i class="fas fa-shipping-fast"></i> Status da Entrega</h2>
                
                <div class="tracking-header">
                    <div>
                        <div class="info-label">Código de Rastreio</div>
                        <div class="tracking-number" id="tracking-code">CT123456789BR</div>
                    </div>
                    <div class="tracking-status" id="delivery-status">Em Trânsito</div>
                </div>
                
                <div class="tracking-steps">
                    <!-- Pedido Confirmado -->
                    <div class="tracking-step">
                        <div class="step-icon completed">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="step-content">
                            <div class="step-title">Pedido Confirmado</div>
                            <div class="step-description">Seu pedido foi processado e confirmado</div>
                            <div class="step-date" id="step1-date">20/12/2024 10:30</div>
                        </div>
                    </div>
                    
                    <!-- Preparando para Envio -->
                    <div class="tracking-step">
                        <div class="step-icon completed">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="step-content">
                            <div class="step-title">Preparando para Envio</div>
                            <div class="step-description">Seu produto está sendo preparado para envio</div>
                            <div class="step-date" id="step2-date">21/12/2024 14:15</div>
                        </div>
                    </div>
                    
                    <!-- Em Trânsito -->
                    <div class="tracking-step">
                        <div class="step-icon active">
                            <i class="fas fa-shipping-fast"></i>
                        </div>
                        <div class="step-content">
                            <div class="step-title">Em Trânsito</div>
                            <div class="step-description">Seu pedido saiu para entrega</div>
                            <div class="step-date" id="step3-date">22/12/2024 09:00</div>
                        </div>
                    </div>
                    
                    <!-- Saiu para Entrega -->
                    <div class="tracking-step">
                        <div class="step-icon pending">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div class="step-content">
                            <div class="step-title">Saiu para Entrega</div>
                            <div class="step-description">Entregador a caminho do endereço</div>
                            <div class="step-date" id="step4-date">-</div>
                        </div>
                    </div>
                    
                    <!-- Entregue -->
                    <div class="tracking-step">
                        <div class="step-icon pending">
                            <i class="fas fa-home"></i>
                        </div>
                        <div class="step-content">
                            <div class="step-title">Entregue</div>
                            <div class="step-description">Produto entregue com sucesso</div>
                            <div class="step-date" id="step5-date">-</div>
                        </div>
                    </div>
                </div>
                
                <div class="action-buttons">
                    <a href="https://www.correios.com.br" target="_blank" class="btn-primary">
                        <i class="fas fa-external-link-alt"></i> Rastrear nos Correios
                    </a>
                    <button class="btn-secondary" id="print-receipt-btn">
                        <i class="fas fa-print"></i> Imprimir Comprovante
                    </button>
                </div>
            </div>
            
            <!-- ================= ORDER INFO CARD ================= -->
            <div class="order-info-card">
                <h2 class="card-title"><i class="fas fa-shopping-bag"></i> Detalhes do Pedido</h2>
                
                <div class="order-info">
                    <div class="info-group">
                        <span class="info-label">Número do Pedido</span>
                        <span class="info-value" id="order-number">CT2024001</span>
                    </div>
                    
                    <div class="info-group">
                        <span class="info-label">Data do Pedido</span>
                        <span class="info-value" id="order-date">20/12/2024</span>
                    </div>
                    
                    <div class="info-group">
                        <span class="info-label">Previsão de Entrega</span>
                        <span class="info-value delivery-date" id="estimated-delivery">25/12/2024</span>
                    </div>
                </div>
                
                <h3 style="color: #fff; margin: 25px 0 15px; font-size: 18px;">Produtos</h3>
                <div class="order-items" id="order-items">
                    <!-- Itens serão inseridos via JavaScript -->
                </div>
                
                <div class="shipping-info">
                    <h4><i class="fas fa-map-marker-alt"></i> Endereço de Entrega</h4>
                    <div class="shipping-address" id="shipping-address">
                        Carregando endereço...
                    </div>
                    <div class="estimated-delivery">
                        <div class="info-label">Previsão de Entrega</div>
                        <div class="delivery-date" id="delivery-date-display">25/12/2024</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- ================= SUPPORT SECTION ================= -->
        <div class="support-section">
            <h3 class="support-title">Precisa de Ajuda?</h3>
            <div class="support-options">
                <div class="support-option">
                    <div class="support-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h4>Atendimento ao Cliente</h4>
                    <p>Nossa equipe está disponível para ajudar com qualquer dúvida sobre seu pedido.</p>
                    <a href="contato.html" class="support-link">
                        Falar com Atendimento <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="support-option">
                    <div class="support-icon">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <h4>Perguntas Frequentes</h4>
                    <p>Encontre respostas para as dúvidas mais comuns sobre entregas e rastreamento.</p>
                    <a href="faq.php" class="support-link">
                        Ver FAQ <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="support-option">
                    <div class="support-icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <h4>Telefone de Contato</h4>
                    <p>Fale diretamente com nosso time de suporte para questões urgentes.</p>
                        <a href="https://wa.me/5512997291076?text=Olá,%20gostaria%20de%20informações%20sobre%20o%20rastreamento%20do%20meu%20pedido%20na%20Ciclotech" class="support-link">
                            (12) 99729-1076 <i class="fas fa-phone"></i>
                        </a>
                </div>
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
    
    <!-- Rodapé para impressão (oculto na visualização normal) -->
    <div class="print-footer print-only">
        <p>Este comprovante foi gerado automaticamente pelo sistema Ciclotech.</p>
        <p>Em caso de dúvidas, entre em contato: (12) 99729-1076 | contato@ciclotech.com.br</p>
    </div>
    
    <script>
        // ======================= CARREGAR DADOS DO PEDIDO =======================
        function loadOrderData() {
            const orderData = JSON.parse(localStorage.getItem('ultimoPedido'));
            
            if (orderData) {
                // Atualizar informações do pedido
                document.getElementById('order-number').textContent = orderData.id;
                document.getElementById('order-date').textContent = new Date(orderData.data).toLocaleDateString('pt-BR');
                
                // Gerar código de rastreio baseado no ID do pedido
                const trackingCode = 'CT' + orderData.id.substr(2) + 'BR';
                document.getElementById('tracking-code').textContent = trackingCode;
                
                // Calcular previsão de entrega (5 dias úteis)
                const orderDate = new Date(orderData.data);
                const deliveryDate = new Date(orderDate);
                deliveryDate.setDate(orderDate.getDate() + 5);
                document.getElementById('estimated-delivery').textContent = deliveryDate.toLocaleDateString('pt-BR');
                document.getElementById('delivery-date-display').textContent = deliveryDate.toLocaleDateString('pt-BR');
                
                // Atualizar endereço de entrega
                if (orderData.endereco) {
                    const address = orderData.endereco;
                    document.getElementById('shipping-address').innerHTML = `
                        <strong>${address.endereco}, ${address.numero}</strong><br>
                        ${address.complemento ? address.complemento + '<br>' : ''}
                        ${address.bairro}<br>
                        ${address.cidade} - ${address.estado}<br>
                        CEP: ${address.cep}
                    `;
                }
                
                // Renderizar itens do pedido
                renderOrderItems(orderData.itens);
                
                // Simular datas de atualização do rastreio
                simulateTrackingDates(orderDate);
            } else {
                // Caso não haja dados, mostrar mensagem
                document.getElementById('order-items').innerHTML = '<p>Não foi possível carregar os detalhes do pedido.</p>';
                document.getElementById('shipping-address').textContent = 'Nenhum endereço encontrado';
            }
        }
        
        // ======================= RENDERIZAR ITENS DO PEDIDO =======================
        function renderOrderItems(itens) {
            const orderItems = document.getElementById('order-items');
            
            orderItems.innerHTML = "";
            
            if (!itens || itens.length === 0) {
                orderItems.innerHTML = '<p>Nenhum item encontrado no pedido</p>';
                return;
            }
            
            itens.forEach(item => {
                const orderItem = document.createElement("div");
                orderItem.classList.add("order-item");
                orderItem.innerHTML = `
                    <img src="${item.img}" alt="${item.nome}" onerror="this.src='../img/placeholder-product.jpg'">
                    <div class="item-details">
                        <div class="item-name">${item.nome}</div>
                        <div class="item-price">R$${item.preco.toFixed(2)}</div>
                        <div class="item-quantity">Quantidade: ${item.quantidade}</div>
                        <div class="item-sku">SKU: ${generateSKU(item.nome)}</div>
                    </div>
                `;
                orderItems.appendChild(orderItem);
            });
        }
        
        // ======================= GERAR SKU A PARTIR DO NOME =======================
        function generateSKU(productName) {
            // Gerar um SKU simples baseado no nome do produto
            const words = productName.toUpperCase().split(' ');
            let sku = '';
            
            for (let i = 0; i < Math.min(3, words.length); i++) {
                if (words[i].length > 0) {
                    sku += words[i].substr(0, 3);
                }
            }
            
            // Adicionar números aleatórios
            sku += Math.floor(100 + Math.random() * 900);
            return sku;
        }
        
        // ======================= SIMULAR DATAS DE RASTREIO =======================
        function simulateTrackingDates(orderDate) {
            // Pedido Confirmado (mesmo dia)
            document.getElementById('step1-date').textContent = 
                orderDate.toLocaleDateString('pt-BR') + ' ' + 
                orderDate.toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'});
            
            // Preparando para Envio (1 dia depois)
            const prepDate = new Date(orderDate);
            prepDate.setDate(orderDate.getDate() + 1);
            prepDate.setHours(14, 15, 0, 0);
            document.getElementById('step2-date').textContent = 
                prepDate.toLocaleDateString('pt-BR') + ' ' + 
                prepDate.toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'});
            
            // Em Trânsito (2 dias depois)
            const transitDate = new Date(orderDate);
            transitDate.setDate(orderDate.getDate() + 2);
            transitDate.setHours(9, 0, 0, 0);
            document.getElementById('step3-date').textContent = 
                transitDate.toLocaleDateString('pt-BR') + ' ' + 
                transitDate.toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'});
            
            // Simular atualização automática do status
            simulateRealTimeUpdates();
        }
        
        // ======================= SIMULAR ATUALIZAÇÕES EM TEMPO REAL =======================
        function simulateRealTimeUpdates() {
            // Simular que o status pode mudar com o tempo
            const statusElement = document.getElementById('delivery-status');
            const steps = document.querySelectorAll('.tracking-step');
            
            // Array de possíveis status
            const statuses = [
                { text: 'Processando', class: 'processing' },
                { text: 'Em Separação', class: 'preparing' },
                { text: 'Em Trânsito', class: 'transit' },
                { text: 'Saiu para Entrega', class: 'out-for-delivery' },
                { text: 'Entregue', class: 'delivered' }
            ];
            
            // Simular progresso a cada 10 segundos (apenas para demonstração)
            let currentStep = 2; // Começa no passo 3 (Em Trânsito)
            
            const progressInterval = setInterval(() => {
                if (currentStep < steps.length) {
                    // Atualizar status
                    statusElement.textContent = statuses[currentStep].text;
                    
                    // Atualizar ícones dos passos
                    steps[currentStep].querySelector('.step-icon').classList.remove('pending');
                    steps[currentStep].querySelector('.step-icon').classList.add('completed');
                    
                    if (currentStep + 1 < steps.length) {
                        steps[currentStep + 1].querySelector('.step-icon').classList.remove('pending');
                        steps[currentStep + 1].querySelector('.step-icon').classList.add('active');
                    }
                    
                    // Atualizar data do passo atual
                    const now = new Date();
                    document.getElementById(`step${currentStep + 1}-date`).textContent = 
                        now.toLocaleDateString('pt-BR') + ' ' + 
                        now.toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'});
                    
                    currentStep++;
                    
                    // Se chegou ao final, parar o intervalo
                    if (currentStep >= steps.length) {
                        clearInterval(progressInterval);
                    }
                }
            }, 10000); // 10 segundos para demonstração
        }
        
        // ======================= FUNÇÃO DE IMPRESSÃO MELHORADA =======================
        function printReceipt() {
            // Atualizar data de emissão no cabeçalho de impressão
            const now = new Date();
            document.getElementById('print-date').textContent = 
                now.toLocaleDateString('pt-BR') + ' ' + 
                now.toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'});
            
            // Preparar página para impressão
            const originalTitle = document.title;
            document.title = "Ciclotech - Comprovante de Rastreio";
            
            // Executar impressão
            window.print();
            
            // Restaurar título original após impressão
            document.title = originalTitle;
        }
        
        // ======================= INICIALIZAÇÃO =======================
        document.addEventListener("DOMContentLoaded", function() {
            loadOrderData();
            
            // Adicionar evento de clique ao botão de impressão
            document.getElementById('print-receipt-btn').addEventListener('click', printReceipt);
        });
    </script>
</body>
</html>