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
$produto = null;
$mensagem = '';

// Verificar se foi passado ID do produto
if (isset($_GET['id_produto'])) {
    $id = (int)$_GET['id_produto'];
    
    // Buscar informações do produto
    $sql = "SELECT * FROM produtos WHERE id_produto = $id";
    $resultado = mysqli_query($conexao, $sql);
    
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $produto = mysqli_fetch_assoc($resultado);
    } else {
        $mensagem = "<div class='erro'>Produto não encontrado!</div>";
    }
} else {
    $mensagem = "<div class='erro'>ID do produto não especificado!</div>";
}

// Processar o formulário de atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar'])) {
    $id = (int)$_POST['id_produto'];
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
        $sql = "UPDATE produtos SET 
                nome_produto = '$nome',
                preco = $preco,
                estado = '$estado',
                quantidade_estoque = $quantidade
                WHERE id_produto = $id";
        
        if (mysqli_query($conexao, $sql)) {
            $mensagem = "<div class='sucesso'>Produto atualizado com sucesso!</div>";
            
            // Recarregar dados atualizados
            $sql = "SELECT * FROM produtos WHERE id_produto = $id";
            $resultado = mysqli_query($conexao, $sql);
            $produto = mysqli_fetch_assoc($resultado);
        } else {
            $mensagem = "<div class='erro'>Erro ao atualizar: " . mysqli_error($conexao) . "</div>";
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
    <title>Editar Produto - Peças de Computador</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #1a2a6c, #2a5298);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .edit-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 800px;
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(to right, #1a2a6c, #2a5298);
            color: white;
            padding: 25px;
            text-align: center;
            position: relative;
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .header p {
            opacity: 0.9;
        }
        
        .user-info {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
        }
        
        .content {
            padding: 30px;
        }
        
        .mensagem {
            margin-bottom: 25px;
        }
        
        .sucesso {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #28a745;
        }
        
        .erro {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
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
            padding: 14px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        input:focus, select:focus {
            border-color: #1a2a6c;
            box-shadow: 0 0 0 3px rgba(26, 42, 108, 0.1);
            outline: none;
        }
        
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-col {
            flex: 1;
        }
        
        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }
        
        .btn {
            flex: 1;
            padding: 15px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
            text-decoration: none;
        }
        
        .btn-primary {
            background: linear-gradient(to right, #1a2a6c, #2a5298);
            color: white;
        }
        
        .btn-secondary {
            background: #e0e0e0;
            color: #333;
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .btn-primary:hover {
            background: linear-gradient(to right, #152155, #1a2a6c);
        }
        
        .btn-secondary:hover {
            background: #d0d0d0;
        }
        
        .product-image {
            width: 120px;
            height: 120px;
            background: #f0f0f0;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
            color: #1a2a6c;
        }
        
        .product-id {
            text-align: center;
            font-size: 18px;
            color: #777;
            margin-bottom: 20px;
        }
        
        .form-section {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        
        .form-section h2 {
            color: #1a2a6c;
            margin-bottom: 15px;
            font-size: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        @media (max-width: 600px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .btn-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="edit-container">
        <div class="header">
            <h1>Editar Produto</h1>
            <p>Atualize as informações da peça de computador</p>
        </div>
        
        <div class="content">
            
            <?php if ($produto): ?>
            <form method="POST" action="alterar.php">
                <input type="hidden" name="id_produto" value="<?= $produto['id_produto'] ?>">
                
                <div class="product-id">
                    ID do Produto: #<?= $produto['id_produto'] ?>
                </div>
                
                <div class="form-section">
                    <h2>Informações Básicas</h2>
                    <div class="form-group">
                        <label for="nome_produto">Nome do Produto</label>
                        <input type="text" id="nome_produto" name="nome_produto" 
                               value="<?= htmlspecialchars($produto['nome_produto']) ?>" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="preco">Preço (R$)</label>
                                <input type="number" id="preco" name="preco" step="0.01" min="0.01"
                                       value="<?= $produto['preco'] ?>" required>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="quantidade_estoque">Quantidade em Estoque</label>
                                <input type="number" id="quantidade_estoque" name="quantidade_estoque" min="0"
                                       value="<?= $produto['quantidade_estoque'] ?>" required>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-section">
                    <h2>Status do Produto</h2>
                    <div class="form-group">
                        <label for="estado">Estado do Produto</label>
                        <select id="estado" name="estado" required>
                            <option value="disponivel" <?= $produto['estado'] == 'disponivel' ? 'selected' : '' ?>>Disponível</option>
                            <option value="vendido" <?= $produto['estado'] == 'vendido' ? 'selected' : '' ?>>Vendido</option>
                        </select>
                    </div>
                    
                    
                </div>
                
                <div class="btn-group">
                    <button type="submit" name="atualizar" class="btn btn-primary">Atualizar Produto</button>
                    <a href="listaProdutos.php" class="btn btn-secondary">Voltar para Lista</a>
                </div>
            </form>
            <?php else: ?>
                <div class="erro">Não foi possível carregar as informações do produto.</div>
                <div class="btn-group" style="margin-top: 20px;">
                    <a href="listaProdutos.php" class="btn btn-secondary">Voltar para Lista</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Validação do preço
        document.getElementById('preco').addEventListener('blur', function() {
            if (this.value <= 0) {
                alert('O preço deve ser maior que zero!');
                this.value = 1;
                this.focus();
            }
        });
        
        // Validação do estoque
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