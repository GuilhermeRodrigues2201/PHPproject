<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit();
}

// Conexão com o banco de dados
$conexao = mysqli_connect("localhost", "root", "", "sistema_funcionarios");

// Verificar conexão
if (!$conexao) {
    die("Erro na conexão: " . mysqli_connect_error());
}

// Função para sanitizar entradas
function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Obter dados do usuário logado
$usuario_id = $_SESSION['usuario']['id'];
$mensagem = '';

// Buscar dados atuais do funcionário
$funcionario = null;
$sql = "SELECT * FROM funcionarios WHERE idFunc = $usuario_id";
$resultado = mysqli_query($conexao, $sql);

if ($resultado && mysqli_num_rows($resultado) > 0) {
    $funcionario = mysqli_fetch_assoc($resultado);
} else {
    $mensagem = "<p class='erro'>Erro ao carregar dados do usuário!</p>";
}

// Processar atualização
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['atualizar'])) {
    $nickname = sanitizeInput($_POST['nickname']);
    $senha = sanitizeInput($_POST['senha']);
    $nomeCompleto = sanitizeInput($_POST['nomeCompleto']);
    $email = sanitizeInput($_POST['email']);
    
    // Atualizar senha apenas se foi fornecida
    $senhaUpdate = '';
    if (!empty($senha)) {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $senhaUpdate = ", senha = '$senhaHash'";
    }
    
    $sql = "UPDATE funcionarios SET 
            nickname = '$nickname', 
            nomeCompleto = '$nomeCompleto', 
            email = '$email'
            $senhaUpdate
            WHERE idFunc = $usuario_id";
    
    if (mysqli_query($conexao, $sql)) {
        $mensagem = "<p class='sucesso'>Perfil atualizado com sucesso!</p>";
        
        // Atualizar dados na sessão
        $_SESSION['usuario']['nickname'] = $nickname;
        
        // Recarregar dados atualizados
        $sql = "SELECT * FROM funcionarios WHERE idFunc = $usuario_id";
        $resultado = mysqli_query($conexao, $sql);
        $funcionario = mysqli_fetch_assoc($resultado);
    } else {
        $mensagem = "<p class='erro'>Erro ao atualizar: " . mysqli_error($conexao) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meu Perfil</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .management-container {
            max-width: 800px;
            margin: 50px auto;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            padding: 30px;
        }
        
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .user-info-card {
            background-color: #f5f9ff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(to right, #1e5799, #2989d8);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 32px;
            flex-shrink: 0;
        }
        
        .user-details {
            flex-grow: 1;
        }
        
        .user-name {
            font-size: 24px;
            font-weight: bold;
            color: #1e5799;
            margin-bottom: 5px;
        }
        
        .user-role {
            background-color: #1e5799;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            display: inline-block;
            margin-bottom: 10px;
        }
        
        .user-email {
            color: #555;
            font-size: 16px;
        }
        
        .form-container {
            margin-top: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #1e5799;
        }
        
        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
        
        /* ESTILO UNIFICADO PARA TODOS OS BOTÕES */
        .btn-azul {
            background: linear-gradient(to right, #1e5799, #2989d8);
            color: white;
            border: none;
            border-radius: 5px;
            padding: 12px 25px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            margin-top: 10px;
        }
        
        .btn-azul:hover {
            background: linear-gradient(to right, #163e6f, #1e5799);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .btn-largo {
            width: 100%;
            padding: 14px;
            font-size: 17px;
            font-weight: 600;
        }
        
        .btn-medio {
            width: auto;
            min-width: 200px;
            padding: 12px 20px;
        }
        
        .erro {
            background-color: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .sucesso {
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .btn-container {
            text-align: center;
            margin-top: 25px;
        }
        
        .password-strength {
            margin-top: 5px;
            font-size: 14px;
            color: #555;
        }
        
        .strength-weak { color: #e74c3c; }
        .strength-medium { color: #f39c12; }
        .strength-strong { color: #27ae60; }
    </style>
    <script>
        function checkPasswordStrength() {
            const password = document.getElementById('senha').value;
            const strengthText = document.getElementById('password-strength');
            
            if (password.length === 0) {
                strengthText.textContent = '';
                return;
            }
            
            // Verificar força da senha
            let strength = 0;
            
            // Comprimento mínimo
            if (password.length >= 8) strength += 1;
            
            // Letras minúsculas e maiúsculas
            if (/[a-z]/.test(password)) strength += 1;
            if (/[A-Z]/.test(password)) strength += 1;
            
            // Números e caracteres especiais
            if (/[0-9]/.test(password)) strength += 1;
            if (/[^A-Za-z0-9]/.test(password)) strength += 1;
            
            // Classificar a força
            if (strength <= 2) {
                strengthText.textContent = 'Senha fraca';
                strengthText.className = 'password-strength strength-weak';
            } else if (strength === 3) {
                strengthText.textContent = 'Senha média';
                strengthText.className = 'password-strength strength-medium';
            } else {
                strengthText.textContent = 'Senha forte';
                strengthText.className = 'password-strength strength-strong';
            }
        }
    </script>
</head>
<body>
    <div class="management-container">
        <div class="header-section">
            <h1>Meu Perfil</h1>
        </div>
        
        <?php if ($funcionario): ?>
        <div class="user-info-card">
            <div class="user-avatar">
                <?= strtoupper(substr($funcionario['nomeCompleto'], 0, 1)) ?>
            </div>
            <div class="user-details">
                <div class="user-name"><?= htmlspecialchars($funcionario['nomeCompleto']) ?></div>
                <div class="user-role"><?= ucfirst($funcionario['tipo']) ?></div>
                <div class="user-email"><?= htmlspecialchars($funcionario['email']) ?></div>
            </div>
        </div>
        <?php endif; ?>
        
        <?= $mensagem ?>
        
        <div class="form-container">
            <form method="POST">
                <div class="form-group">
                    <label for="nickname">Nickname:</label>
                    <input type="text" name="nickname" id="nickname" 
                           value="<?= $funcionario ? htmlspecialchars($funcionario['nickname']) : '' ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="senha">Nova Senha (deixe em branco para manter a atual):</label>
                    <input type="password" name="senha" id="senha" onkeyup="checkPasswordStrength()">
                    <div id="password-strength" class="password-strength"></div>
                </div>
                
                <div class="form-group">
                    <label for="nomeCompleto">Nome Completo:</label>
                    <input type="text" name="nomeCompleto" id="nomeCompleto" 
                           value="<?= $funcionario ? htmlspecialchars($funcionario['nomeCompleto']) : '' ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" 
                           value="<?= $funcionario ? htmlspecialchars($funcionario['email']) : '' ?>" required>
                </div>
                
                <button type="submit" name="atualizar" class="btn-azul btn-largo">Atualizar Perfil</button>
            </form>
        </div>
        
        <div class="btn-container">
            <a href="../<?= ($_SESSION['usuario']['tipo'] === 'repositor' ? 'repositor/estoque.php' : 'funcionarios/perfil.php') ?>" 
               class="btn-azul btn-medio">
                Voltar
            </a>
            <a href="../logout.php" class="btn-azul btn-medio">Sair do Sistema</a>
        </div>
    </div>
</body>
</html>
<?php
mysqli_close($conexao);
?>