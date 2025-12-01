<?php
// user-sidebar.php
$current_page = basename($_SERVER['PHP_SELF']);

// Define a foto do usuário
$foto_usuario = 'https://ui-avatars.com/api/?name=' . urlencode($_SESSION['admin_nome'] ?? 'Admin') . '&background=dc2626&color=fff&size=120';

// Se existir foto personalizada, usa ela
if (isset($_SESSION['admin_foto'])) {
    $foto_personalizada = '../assets_adms' . $_SESSION['admin_foto'];
    
    // Verifica se a foto existe localmente (se estiver usando arquivos locais)
    if (file_exists($foto_personalizada)) {
        $foto_usuario = $foto_personalizada;
    } else {
        // Se não encontrar localmente, tenta usar o caminho direto
        $foto_usuario = $foto_personalizada;
    }
}
?>
<div class="user-card-container">
    <div class="user-card">
        <div class="user-avatar">
            <img src="<?= $foto_usuario ?>" alt="Avatar" onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['admin_nome'] ?? 'Admin') ?>&background=dc2626&color=fff&size=120'">
        </div>
        <div class="user-info">
            <h3 class="user-name"><?= $_SESSION['admin_nome'] ?? 'Administrador' ?></h3>
            <p class="user-role">ADMINISTRADOR</p>
        </div>
        <!-- Links de navegação dentro do card -->
        <div class="user-links">
            <a href="dashboard.php" class="nav-link <?= $current_page == 'dashboard.php' ? 'active' : '' ?>">Início</a>
            <a href="pedidos.php" class="nav-link <?= $current_page == 'pedidos.php' ? 'active' : '' ?>">Pedidos</a>
            <a href="clientes.php" class="nav-link <?= $current_page == 'clientes.php' ? 'active' : '' ?>">Clientes</a>
            <a href="produtos.php" class="nav-link <?= $current_page == 'produtos.php' ? 'active' : '' ?>">Produtos</a>
            <a href="relatorios.php" class="nav-link <?= $current_page == 'relatorios.php' ? 'active' : '' ?>">Relatórios</a>
            <a href="logout.php" class="nav-link sair-link">Sair</a>
        </div>
    </div>
</div>