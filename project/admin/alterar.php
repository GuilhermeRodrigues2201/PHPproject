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

// Processar ações
$mensagem = '';
$funcionario = null;

// Processar busca de funcionário
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id_funcionario'])) {
    $id = (int) sanitizeInput($_GET['id_funcionario']);
    $sql = "SELECT * FROM funcionarios WHERE idFunc = $id";
    $resultado = mysqli_query($conexao, $sql);
    
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $funcionario = mysqli_fetch_assoc($resultado);
    } else {
        $mensagem = "<p class='erro'>Funcionário não encontrado.</p>";
    }
}

// Processar atualização de funcionário
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['novoid_funcionario'])) {
    $novoid = (int) sanitizeInput($_POST['novoid_funcionario']);
    $nickname = sanitizeInput($_POST['nickname']);
    $senha = sanitizeInput($_POST['senha']);
    $nomeCompleto = sanitizeInput($_POST['nomeCompleto']);
    $email = sanitizeInput($_POST['email']);
    $tipo = sanitizeInput($_POST['tipo']);
    
    // Atualizar senha apenas se foi fornecida
    $senhaUpdate = '';
    if (!empty($senha)) {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $senhaUpdate = ", senha = '$senhaHash'";
    }
    
    $sql = "UPDATE funcionarios SET 
            nickname = '$nickname', 
            nomeCompleto = '$nomeCompleto', 
            email = '$email',
            tipo = '$tipo'
            $senhaUpdate
            WHERE idFunc = $novoid";
    
    if (mysqli_query($conexao, $sql)) {
        $mensagem = "<p class='sucesso'>Funcionário atualizado com sucesso!</p>";
        // Recarregar dados atualizados
        $sql = "SELECT * FROM funcionarios WHERE idFunc = $novoid";
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
    <title>Alterar Funcionário</title>
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
        input[type="number"],
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
    </style>
</head>
<body>
    <div class="management-container">
        <h1>Alterar Funcionário</h1>
        
        <?= $mensagem ?>
        
        <div class="form-container">
            <!-- Formulário de busca -->
            <form method="GET">
                <div class="form-group">
                    <label for="id_funcionario">ID do Funcionário:</label>
                    <input type="number" name="id_funcionario" id="id_funcionario" required
                           value="<?= isset($_GET['id_funcionario']) ? htmlspecialchars($_GET['id_funcionario']) : '' ?>">
                </div>
                <button type="submit" class="btn-azul btn-largo">Buscar Funcionário</button>
            </form>
            
            <!-- Formulário de edição -->
            <?php if ($funcionario): ?>
            <form method="POST" style="margin-top: 30px;">
                <input type="hidden" name="novoid_funcionario" value="<?= $funcionario['idFunc'] ?>">
                
                <div class="form-group">
                    <label for="nickname">Nickname:</label>
                    <input type="text" name="nickname" id="nickname" 
                           value="<?= htmlspecialchars($funcionario['nickname']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="senha">Nova Senha (deixe em branco para manter a atual):</label>
                    <input type="password" name="senha" id="senha">
                </div>
                
                <div class="form-group">
                    <label for="nomeCompleto">Nome Completo:</label>
                    <input type="text" name="nomeCompleto" id="nomeCompleto" 
                           value="<?= htmlspecialchars($funcionario['nomeCompleto']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" 
                           value="<?= htmlspecialchars($funcionario['email']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="tipo">Tipo de Usuário:</label>
                    <select name="tipo" id="tipo" required>
                        <option value="gerente" <?= $funcionario['tipo'] === 'gerente' ? 'selected' : '' ?>>Gerente</option>
                        <option value="repositor" <?= $funcionario['tipo'] === 'repositor' ? 'selected' : '' ?>>Repositor</option>
                        <option value="funcionario" <?= $funcionario['tipo'] === 'funcionario' ? 'selected' : '' ?>>Funcionário</option>
                    </select>
                </div>
                
                <button type="submit" class="btn-azul btn-largo">Atualizar Funcionário</button>
            </form>
            <?php endif; ?>
        </div>
        
        <div class="btn-container">
            <a href="dashboard.php" class="btn-azul btn-medio btn-voltar">Voltar ao Dashboard</a>
        </div>
    </div>
</body>
</html>
<?php
mysqli_close($conexao);
?>