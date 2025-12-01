# Ciclotech - E-commerce de Ciclismo

## Descrição
Ciclotech é um e-commerce especializado em produtos de ciclismo, oferecendo uma variedade de bicicletas, kits, acessórios e equipamentos para diferentes modalidades de ciclismo.

## Funcionalidades Principais

### Autenticação de Usuários
- Sistema de login/logout
- Perfil de usuário com foto
- Sessões para manter usuários logados

### Carrinho de Compras
- Adicionar/remover produtos
- Alterar quantidades
- Carrinho lateral interativo
- Persistência de dados com localStorage
- Cálculo automático do total

### Sistema de Busca
- Barra de pesquisa no header
- Overlay de pesquisa avançada
- Busca por nome e preço
- Exibição de resultados em tempo real

### Catálogo de Produtos
- Produtos em destaque
- Categorias por modalidade (estrada, montanha, trilha, cidade)
- Promoções especiais
- Informações detalhadas de preços (à vista, parcelado, desconto no PIX)

## Estrutura do Projeto

```
/
├── html/
│   ├── homepage.php          # Página principal
│   ├── login.php             # Página de login
│   ├── usuario.php           # Perfil do usuário
│   ├── logout.php            # Logout do sistema
│   └── outras páginas .html  # Páginas de produtos e categorias
├── css/
│   └── homepage.css          # Estilos principais
├── img/                      # Imagens e logos
├── uploads/                  # Fotos de perfil dos usuários
├── auth.php                  # Sistema de autenticação
├── flash.php                 # Sistema de mensagens flash
└── README.md                 # Este arquivo
```

## Tecnologias Utilizadas

### Frontend
- HTML5
- CSS3
- JavaScript Vanilla
- Font Awesome (ícones)
- Google Fonts (Anton, Poppins)

### Backend
- PHP 7.4+
- MySQL
- Sessões PHP

## Requisitos do Sistema

### Servidor Web
- Apache ou Nginx
- PHP 7.4 ou superior
- MySQL 5.7 ou superior

### Configuração do Banco de Dados
1. Criar banco de dados chamado `ciclotech`
2. Importar estrutura das tabelas (clientes, produtos, etc.)
3. Configurar conexão no arquivo de autenticação

### Permissões de Diretório
```
/uploads/       - Deve ter permissão de escrita (chmod 755)
session data    - Diretório de sessões configurado no PHP
```

## Instalação

1. Clone o repositório para o diretório do seu servidor web:
```bash
git clone [url-do-repositorio] /var/www/html/ciclotech
```

2. Configure o banco de dados MySQL:
```sql
CREATE DATABASE ciclotech;
USE ciclotech;

-- Tabela de clientes (exemplo básico)
CREATE TABLE clientes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100),
    email VARCHAR(100),
    senha VARCHAR(255),
    foto_perfil VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

3. Configure as credenciais de banco de dados em `auth.php`:
```php
$conn = new mysqli("localhost", "seu_usuario", "sua_senha", "ciclotech");
```

4. Configure o diretório de uploads:
```bash
chmod 755 uploads/
```

## Funcionalidades de Usuário

### Usuário Não Logado
- Navegar pelo catálogo
- Pesquisar produtos
- Visualizar produtos
- Adicionar ao carrinho (com redirecionamento para login)

### Usuário Logado
- Todas funcionalidades anteriores
- Finalizar compra
- Acessar perfil
- Visualizar foto no header
- Sistema de checkout

## Segurança

### Implementado
- Sanitização de outputs com `htmlspecialchars()`
- Prepared statements para consultas SQL
- Validação de sessões
- Proteção contra XSS básica
- Logout seguro

### Recomendações Adicionais
1. Implementar HTTPS
2. Adicionar CSRF tokens
3. Validar entrada de dados
4. Implementar rate limiting
5. Usar hash forte para senhas (bcrypt)

## Carrinho de Compras

### Armazenamento
- Dados armazenados no `localStorage`
- Estrutura: array de objetos com:
  - nome: nome do produto
  - preco: preço unitário
  - quantidade: quantidade selecionada
  - img: URL da imagem

### Funcionalidades
- Adicionar produto: `adicionarAoCarrinho(nome, preco, img)`
- Remover produto: `removerItem(index)`
- Alterar quantidade: `alterarQtd(index, delta)`
- Calcular total: soma automática

## Sistema de Busca

### Duas Interfaces
1. **Barra de Pesquisa no Header**: busca simples
2. **Overlay de Pesquisa**: busca avançada com categorias

### Funcionalidades
- Busca em tempo real
- Filtro por nome e preço
- Exibição de resultados com layout responsivo
- Categorias pré-definidas

## Estilos e Design

### Características
- Design responsivo
- Paleta de cores preto/branco/vermelho
- Layout em grid para produtos
- Header fixo com navegação
- Footer informativo

### Componentes
- Cards de produtos uniformes
- Modal de login/cadastro
- Carrinho lateral deslizante
- Overlay de pesquisa
- Alertas e notificações

## Páginas Específicas

### homepage.php
- Página principal com produtos em destaque
- Categorias por modalidade
- Promoções
- Header com funcionalidades completas

### login.php / cadastro.php
- Formulários de autenticação
- Validação client-side e server-side
- Redirecionamento pós-login

### usuario.php
- Perfil do usuário
- Upload de foto
- Informações pessoais
- Histórico de compras (se implementado)

## Personalização

### Para Adicionar Novos Produtos
1. Criar novo card HTML na seção apropriada
2. Adicionar link para página do produto
3. Incluir imagem, nome, preços
4. Configurar botão "COMPRAR" com `onclick`

### Para Criar Novas Categorias
1. Adicionar item no menu de categorias
2. Criar página HTML específica
3. Adicionar link na seção "Que tipo de ciclista é você?"

## Troubleshooting

### Problemas Comuns

1. **Conexão com Banco de Dados**
   - Verificar credenciais em `auth.php`
   - Testar conexão MySQL
   - Verificar se tabelas existem

2. **Upload de Fotos**
   - Verificar permissões do diretório `uploads/`
   - Verificar tamanho máximo de upload no PHP
   - Verificar extensões permitidas

3. **Sessões Não Funcionam**
   - Verificar configuração de `session.save_path`
   - Verificar se cookies estão habilitados
   - Testar em diferentes navegadores

4. **Carrinho Não Salva**
   - Verificar se localStorage está habilitado
   - Testar em modo normal (não incógnito)
   - Verificar console do navegador por erros JS

## Melhorias Futuras Sugeridas

1. **Backend**
   - Sistema de pagamento integrado
   - Painel administrativo
   - Sistema de pedidos
   - Email de confirmação

2. **Frontend**
   - Página de produto dinâmica
   - Avaliações de produtos
   - Filtros avançados
   - Wishlist

3. **Performance**
   - Cache de imagens
   - Lazy loading
   - Otimização de consultas

4. **SEO**
   - Meta tags dinâmicas
   - Sitemap
   - URL amigáveis

## Suporte

Para suporte ou reportar bugs:
- Abrir issue no repositório
- Contatar equipe de desenvolvimento
- Verificar FAQ na página

## Licença
© 2024 Ciclotech. Todos os direitos reservados.
---

**Nota**: Este é um sistema de demonstração. Para uso em produção, considere implementar medidas de segurança adicionais e testar extensivamente.
