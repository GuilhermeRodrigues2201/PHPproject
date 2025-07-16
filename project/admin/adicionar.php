<?php
session_start();

// Verificar se o usuário é gerente
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'gerente') {
    header("Location: ../index.php");
    exit();
}

// Conexão com o banco de dados de funcionários
$conexao = mysqli_connect("localhost", "root", "", "sistema_funcionarios");

// Verificar conexão
if (!$conexao) {
    die("Erro na conexão: " . mysqli_connect_error());
}

// Função para sanitizar entradas
function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Processar cadastro de novo funcionário
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cadastrar'])) {
    $nickname = sanitizeInput($_POST['nickname']);
    $senha = sanitizeInput($_POST['senha']);
    $nomeCompleto = sanitizeInput($_POST['nomeCompleto']);
    $email = sanitizeInput($_POST['email']);
    $tipo = sanitizeInput($_POST['tipo']);
    
    // Validar campos obrigatórios
    if (empty($nickname) || empty($senha) || empty($nomeCompleto) || empty($email)) {
        $mensagem = "<p class='erro'>Todos os campos são obrigatórios!</p>";
    } else {
        // Verificar se nickname já existe
        $sql_check = "SELECT idFunc FROM funcionarios WHERE nickname = '$nickname'";
        $result_check = mysqli_query($conexao, $sql_check);
        
        if (mysqli_num_rows($result_check) > 0) {
            $mensagem = "<p class='erro'>Nickname já está em uso. Escolha outro.</p>";
        } else {
            
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            
         
            $sql = "INSERT INTO funcionarios (nickname, senha, nomeCompleto, email, tipo) 
                    VALUES ('$nickname', '$senhaHash', '$nomeCompleto', '$email', '$tipo')";
            
            if (mysqli_query($conexao, $sql)) {
                $mensagem = "<p class='sucesso'>Funcionário cadastrado com sucesso!</p>";
                
                
                $_POST = array();
            } else {
                $mensagem = "<p class='erro'>Erro ao cadastrar: " . mysqli_error($conexao) . "</p>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Funcionário</title>
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
        input[type="email"],
        select {
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
        
        .btn-voltar {
            margin-top: 25px;
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
        <h1>Adicionar Novo Funcionário</h1>
        
        <?= $mensagem ?>
        
        <div class="form-container">
            <form method="POST">
                <div class="form-group">
                    <label for="nickname">Nickname:</label>
                    <input type="text" name="nickname" id="nickname" 
                           value="<?= isset($_POST['nickname']) ? htmlspecialchars($_POST['nickname']) : '' ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="senha">Senha:</label>
                    <input type="password" name="senha" id="senha" 
                           required onkeyup="checkPasswordStrength()">
                    <div id="password-strength" class="password-strength"></div>
                </div>
                
                <div class="form-group">
                    <label for="nomeCompleto">Nome Completo:</label>
                    <input type="text" name="nomeCompleto" id="nomeCompleto" 
                           value="<?= isset($_POST['nomeCompleto']) ? htmlspecialchars($_POST['nomeCompleto']) : '' ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" 
                           value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="tipo">Tipo de Usuário:</label>
                    <select name="tipo" id="tipo" required>
                        <option value="gerente" <?= (isset($_POST['tipo']) && $_POST['tipo'] === 'gerente') ? 'selected' : '' ?>>Gerente</option>
                        <option value="repositor" <?= (isset($_POST['tipo']) && $_POST['tipo'] === 'repositor') ? 'selected' : '' ?>>Repositor</option>
                        <option value="funcionario" <?= (!isset($_POST['tipo']) || $_POST['tipo'] === 'funcionario') ? 'selected' : '' ?>>Funcionário</option>
                    </select>
                </div>
                
                <button type="submit" name="cadastrar" class="btn-azul btn-largo">Cadastrar Funcionário</button>
            </form>
        </div>
        
        <div class="btn-container">
            <a href="dashboard.php" class="btn-azul btn-medio btn-voltar">Voltar ao Dashboard</a>
            <a href="listaFuncionarios.php" class="btn-azul btn-medio btn-voltar">Ver Todos os Funcionários</a>
        </div>
    </div>
</body>
</html>
<?php
mysqli_close($conexao);
?>