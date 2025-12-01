<?php
// Iniciar sessão e verificar se o usuário está logado
session_start();

// Verificar se o usuário está logado
if (empty($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ciclotech";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Inicializar array do usuário
$usuario = [
    'nome' => '',
    'email' => '',
    'telefone' => '',
    'cpf' => '',
    'sexo' => '',
    'nascimento' => '',
    'foto_perfil' => ''
];

// Inicializar variáveis de mensagem
$mensagem = '';
$tipoMensagem = '';

// Buscar informações do usuário do banco de dados
$user_id = $_SESSION['usuario_id'];

$sql = "SELECT * FROM clientes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $usuario['nome'] = $row['nome'] ?? '';
    $usuario['email'] = $row['email'] ?? '';
    $usuario['telefone'] = $row['telefone'] ?? '';
    $usuario['cpf'] = $row['cpf'] ?? '';
    $usuario['sexo'] = $row['sexo'] ?? '';
    $usuario['nascimento'] = $row['nascimento'] ?? '';
    $usuario['foto_perfil'] = $row['foto_perfil'] ?? '';
} else {
    // Se não encontrar usuário, destruir sessão e redirecionar
    session_destroy();
    header("Location: login.php");
    exit();
}
$stmt->close();

// Processar upload da foto PRIMEIRO, antes do formulário geral
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
    $fotoTemporaria = $_FILES['avatar']['tmp_name'];
    $nomeArquivo = $_FILES['avatar']['name'];
    $tamanhoArquivo = $_FILES['avatar']['size'];
    $tipoArquivo = $_FILES['avatar']['type'];
    
    // Verificar se é uma imagem
    $permitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!in_array($tipoArquivo, $permitidos)) {
        $mensagem = "Apenas arquivos JPG, JPEG, PNG e GIF são permitidos.";
        $tipoMensagem = "erro";
    } 
    // Verificar tamanho do arquivo (máximo 2MB)
    elseif ($tamanhoArquivo > 2 * 1024 * 1024) {
        $mensagem = "O arquivo deve ter no máximo 2MB.";
        $tipoMensagem = "erro";
    } else {
        // Criar diretório de uploads se não existir
        $diretorioUploads = __DIR__ . '/../uploads/';
        if (!is_dir($diretorioUploads)) {
            if (!mkdir($diretorioUploads, 0755, true)) {
                $mensagem = "Erro ao criar diretório de uploads.";
                $tipoMensagem = "erro";
            }
        }
        
        if (empty($mensagem)) {
            // Gerar nome único para o arquivo
            $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
            $novoNomeArquivo = 'perfil_' . $user_id . '_' . time() . '.' . $extensao;
            $caminhoCompleto = $diretorioUploads . $novoNomeArquivo;
            
            // Mover arquivo para o diretório de uploads
            if (move_uploaded_file($fotoTemporaria, $caminhoCompleto)) {
                // Atualizar no banco de dados
                $sql_foto = "UPDATE clientes SET foto_perfil = ? WHERE id = ?";
                $stmt_foto = $conn->prepare($sql_foto);
                $stmt_foto->bind_param("si", $novoNomeArquivo, $user_id);
                
                if ($stmt_foto->execute()) {
                    $mensagem = "Foto alterada com sucesso!";
                    $tipoMensagem = "sucesso";
                    $usuario['foto_perfil'] = $novoNomeArquivo;
                    
                    // Atualizar também a sessão se necessário
                    $_SESSION['usuario_foto'] = $novoNomeArquivo;
                } else {
                    $mensagem = "Erro ao salvar foto no banco de dados: " . $conn->error;
                    $tipoMensagem = "erro";
                }
                $stmt_foto->close();
            } else {
                $mensagem = "Erro ao fazer upload da foto. Verifique as permissões do diretório.";
                $tipoMensagem = "erro";
            }
        }
    }
}

// Processar o formulário quando enviado (dados gerais)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Processar dados do formulário
    $novo_nome = $_POST['nome'] ?? $usuario['nome'];
    $novo_email = $_POST['email'] ?? $usuario['email'];
    $novo_telefone = $_POST['telefone'] ?? $usuario['telefone'];

    // Atualizar no banco de dados usando o ID
    $sql_update = "UPDATE clientes SET nome = ?, email = ?, telefone = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sssi", $novo_nome, $novo_email, $novo_telefone, $user_id);
    
    if ($stmt_update->execute()) {
        // Se não houve mensagem de upload de foto, mostra mensagem de perfil atualizado
        if (empty($mensagem)) {
            $mensagem = "Perfil atualizado com sucesso!";
            $tipoMensagem = "sucesso";
        }
        
        // Atualizar o array local
        $usuario['nome'] = $novo_nome;
        $usuario['email'] = $novo_email;
        $usuario['telefone'] = $novo_telefone;
        
        // Atualizar também na sessão
        $_SESSION['usuario_nome'] = $novo_nome;
        $_SESSION['usuario_email'] = $novo_email;
    } else {
        $mensagem = "Erro ao atualizar perfil: " . $conn->error;
        $tipoMensagem = "erro";
    }
    $stmt_update->close();
}

$conn->close();
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
    <!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ciclotech</title>
    <link rel="stylesheet" href="../css/pageprodutos.css">
    <link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <header>
        <div class="lado-esquerdo">
            <a href="logout.php" class="sair">
                <i class="fas fa-sign-out-alt"></i> Sair
            <a href="../html/home.php">HOME</a>
        </div>

        <div class="logo">
            <img src="../img/logo.png" alt="Ciclotech">
        </div>

        <div class="carrinho">
            <a href="contato.html" class="botaoContato" title="Fale Conosco">
                <img id="ImgContato" src="../img/contato.png" alt="Contato"></a>
        </div>
    </header>
<center>
    <div class="corpo">
        <br><br>
    <section class="perfil">
    <h2>Meu Perfil</h2>

    <form method="POST" enctype="multipart/form-data">
        <div class="avatar-section">
            <div class="avatar" id="avatarDisplay">
                <?php 
                // Verificar se existe foto de perfil e se o arquivo existe
                if (!empty($usuario['foto_perfil']) && file_exists('../uploads/' . $usuario['foto_perfil'])): 
                ?>
                    <img src="../uploads/<?php echo htmlspecialchars($usuario['foto_perfil']); ?>" alt="Foto de Perfil" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                <?php else: ?>
                    <!-- Imagem padrão quando não há foto -->
                    <img src="../img/user.png" alt="Foto Padrão" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                <?php endif; ?>
            </div>
            <input type="file" name="avatar" id="avatarInput" accept="image/*" style="display: none;">
            <button type="button" class="botao-upload" onclick="document.getElementById('avatarInput').click()">Alterar Foto</button>
        </div>

      <div class="form-perfil">
        <div class="campo">
          <label for="nome">Nome:</label>
          <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
        </div>

        <div class="campo">
          <label for="email">Email:</label>
          <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
        </div>

        <div class="campo">
          <label for="telefone">Telefone:</label>
          <input type="tel" id="telefone" name="telefone" value="<?php echo htmlspecialchars($usuario['telefone']); ?>">
        </div>

        <div class="campo">
          <label for="cpf">CPF:</label>
          <input type="text" id="cpf" value="<?php echo htmlspecialchars($usuario['cpf']); ?>" disabled>
        </div>
      </div>

      <div class="botoes-acao">
        <button type="submit" class="botao">Salvar Alterações</button>
        <a href="home.php" class="botao" style="background: #333;">Cancelar</a>
      </div>
    </form>

    <?php if (isset($mensagem) && !empty($mensagem)): ?>
      <div class="alerta <?php echo $tipoMensagem; ?>">
        <?php echo htmlspecialchars($mensagem); ?>
      </div>
    <?php endif; ?>
  </section>
  <br><br>
    </div>
    </center>
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
// Preview da foto de perfil
document.getElementById('avatarInput').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (event) {
            const avatar = document.querySelector('.avatar');
            avatar.innerHTML = '';
            const img = document.createElement('img');
            img.src = event.target.result;
            img.style.width = '100%';
            img.style.height = '100%';
            img.style.objectFit = 'cover';
            img.style.borderRadius = '50%';
            img.alt = 'Nova Foto de Perfil';
            avatar.appendChild(img);
        };
        reader.readAsDataURL(file);
    }
});
  </script>
</body>
</html>