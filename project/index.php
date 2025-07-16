<?php
session_start();
// Configurações do sistema
define('SISTEMA_NOME', 'Sistema de Funcionários');

// Conexão com o banco de dados
$host = 'localhost';
$dbname = 'sistema_funcionarios';
$username_db = 'root';
$password_db = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

// Função para sanitizar entradas
function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Processar formulário de login
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['nickname']) && isset($_POST['senha'])) {
        $nickname = sanitizeInput($_POST['nickname']);
        $senha = $_POST['senha'];
        
        try {
            $stmt = $pdo->prepare("SELECT * FROM funcionarios WHERE nickname = :nickname");
            $stmt->bindParam(':nickname', $nickname);
            $stmt->execute();
            
            if ($stmt->rowCount() === 0) {
                $mensagem = "<div class='erro'>Usuário não encontrado!</div>";
            } else {
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                
                
                if (password_verify($senha, $usuario['senha'])) {
                    //
                    loginSucesso($usuario);
                } elseif ($senha === $usuario['senha']) {
                  
                    loginSucesso($usuario);
                } else {
                    $mensagem = "<div class='erro'>Senha incorreta!</div>";
                }
            }
        } catch (PDOException $e) {
            $mensagem = "<div class='erro'>Erro no sistema: " . $e->getMessage() . "</div>";
        }
    } else {
        $mensagem = "<div class='erro'>Por favor, preencha todos os campos!</div>";
    }
}


// Função para processar login bem-sucedido
function loginSucesso($usuario) {
    $_SESSION['usuario'] = [
        'id' => $usuario['idFunc'],
        'nickname' => $usuario['nickname'],
        'tipo' => $usuario['tipo'],
        'funcao' => $usuario['funcao']
    ];
    
    // Redirecionar conforme o tipo de usuário
    switch ($usuario['tipo']) {
        case 'gerente':
            header("Location: admin/dashboard.php");
            break;
        case 'repositor':
            header("Location: repositor/estoque.php");
            break;
        default: // funcionario
            header("Location: funcionarios/perfil.php");
    }
    exit();
}

// Se o usuário já está logado, redirecionar
if (isset($_SESSION['usuario'])) {
    switch ($_SESSION['usuario']['tipo']) {
        case 'gerente':
            header("Location: admin/dashboard.php");
            break;
        case 'repositor':
            header("Location: repositor/estoque.php");
            break; 
        default: // funcionario
            header("Location: funcionarios/perfil.php");
    }
    exit();
}
?>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SISTEMA_NOME ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="logo">
        <?= SISTEMA_NOME ?>
        </div>
        <h1>Acesso ao Sistema</h1>
        
        <?= $mensagem ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="nickname">Nickname:</label>
                <input type="text" id="nickname" name="nickname" placeholder="Digite seu nickname" required
                       value="<?= isset($_POST['nickname']) ? htmlspecialchars($_POST['nickname']) : '' ?>">
            </div>
            
            <div class="form-group password-toggle">
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>
            </div>
            
            <button type="submit" class="btn-login">Entrar</button>
        </form> 
        <div class="footer">
        Este site foi desenvolvido por Guilherme
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('senha');
            const toggleIcon = document.querySelector('.toggle-password');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.textContent = '🔒';
            } else {
                passwordField.type = 'password';
                toggleIcon.textContent = '👁️';
            }
        }
    </script>
</body>
</html>
