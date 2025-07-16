<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit();
}

// Conexão com o banco de dados
$conexao = mysqli_connect("localhost", "root", "", "sistema_funcionarios");

if (!$conexao) {
    die("Erro na conexão: " . mysqli_connect_error());
}

// Inicializar variáveis
$mensagem = '';
$dadosFormulario = [
    'nome_produto' => '',
    'preco' => '',
    'estado' => 'disponivel',
    'quantidade_estoque' => 0
];

// Processar o formulário de inserção
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar'])) {
    $nome = mysqli_real_escape_string($conexao, $_POST['nome_produto']);
    $preco = (float)$_POST['preco'];
    $estado = $_POST['estado'];
    $quantidade = (int)$_POST['quantidade_estoque'];
    
    // Validar campos
    $erros = [];
    if (empty($nome)) $erros[] = "Nome do produto é obrigatório";
    if ($preco <= 0) $erros[] = "Preço deve ser maior que zero";
    if ($quantidade < 0) $erros[] = "Quantidade em estoque não pode ser negativa";
    
    if (empty($erros)) {
        $sql = "INSERT INTO produtos (nome_produto, preco, estado, quantidade_estoque, data_cadastro)
                VALUES ('$nome', $preco, '$estado', $quantidade, NOW())";
        
        if (mysqli_query($conexao, $sql)) {
            $mensagem = "<div class='sucesso'>Produto adicionado com sucesso!</div>";
            
            // Limpar formulário após sucesso
            $dadosFormulario = [
                'nome_produto' => '',
                'preco' => '',
                'estado' => 'disponivel',
                'quantidade_estoque' => 0
            ];
        } else {
            $mensagem = "<div class='erro'>Erro ao adicionar: " . mysqli_error($conexao) . "</div>";
        }
    } else {
        $mensagem = "<div class='erro'>" . implode("<br>", $erros) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Produto</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: #f0f2f5;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            padding: 30px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 25px;
        }
        
        .header h1 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .header p {
            color: #7f8c8d;
        }
        
        .mensagem {
            margin-bottom: 20px;
            padding: 12px;
            border-radius: 4px;
        }
        
        .sucesso {
            background-color: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .erro {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        input, select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        input:focus, select:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }
        
        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .btn-primary {
            background: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background: #2980b9;
        }
        
        .btn-secondary {
            background: #ecf0f1;
            color: #2c3e50;
        }
        
        .btn-secondary:hover {
            background: #d0d3d4;
        }
        
        .add-icon {
            text-align: center;
            margin-bottom: 20px;
            color: #3498db;
            font-size: 36px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="add-icon">+</div>
            <h1>Adicionar Novo Produto</h1>
            <p>Preencha os campos abaixo para adicionar um novo produto</p>
        </div>
        
        <?php if (!empty($mensagem)): ?>
            <div class="mensagem"><?= $mensagem ?></div>
        <?php endif; ?>
        
        <form method="POST" action="adicionar.php">
            <div class="form-group">
                <label for="nome_produto">Nome do Produto *</label>
                <input type="text" id="nome_produto" name="nome_produto" 
                       value="<?= htmlspecialchars($dadosFormulario['nome_produto']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="preco">Preço (R$) *</label>
                <input type="number" id="preco" name="preco" step="0.01" min="0.01"
                       value="<?= $dadosFormulario['preco'] ?>" required>
            </div>
            
            <div class="form-group">
                <label for="quantidade_estoque">Quantidade em Estoque *</label>
                <input type="number" id="quantidade_estoque" name="quantidade_estoque" min="0"
                       value="<?= $dadosFormulario['quantidade_estoque'] ?>" required>
            </div>
            
            <div class="form-group">
                <label for="estado">Estado do Produto *</label>
                <select id="estado" name="estado" required>
                    <option value="disponivel" <?= $dadosFormulario['estado'] == 'disponivel' ? 'selected' : '' ?>>Disponível</option>
                    <option value="vendido" <?= $dadosFormulario['estado'] == 'vendido' ? 'selected' : '' ?>>Vendido</option>
                </select>
            </div>
            
            <div class="btn-group">
                <button type="submit" name="adicionar" class="btn btn-primary">Adicionar Produto</button>
                <a href="listaProdutos.php" class="btn btn-secondary">Voltar</a>
            </div>
        </form>
    </div>
    
    <script>
        document.getElementById('preco').addEventListener('blur', function() {
            if (this.value <= 0) {
                alert('O preço deve ser maior que zero!');
                this.value = '';
                this.focus();
            }
        });
        
        document.getElementById('quantidade_estoque').addEventListener('blur', function() {
            if (this.value < 0) {
                alert('A quantidade em estoque não pode ser negativa!');
                this.value = 0;
                this.focus();
            }
        });
    </script>
</body>
</html>
<?php
mysqli_close($conexao);
?>