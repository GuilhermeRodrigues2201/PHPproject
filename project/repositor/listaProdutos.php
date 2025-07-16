<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit();
}

$conexao = mysqli_connect("localhost", "root", "", "sistema_funcionarios");

if (!$conexao) {
    die("Erro na conexão: " . mysqli_connect_error());
}

// Processar exclusão de produto
if (isset($_GET['excluir'])) {
    $id = (int) $_GET['excluir'];
    $sql = "DELETE FROM produtos WHERE id_produto = $id";
    
    if (mysqli_query($conexao, $sql)) {
        $mensagem = "<p class='sucesso'>Produto excluído com sucesso!</p>";
    } else {
        $mensagem = "<p class='erro'>Erro ao excluir: " . mysqli_error($conexao) . "</p>";
    }
}

// Buscar todos os produtos
$produtos = [];
$sql = "SELECT id_produto, nome_produto, preco, estado, quantidade_estoque FROM produtos ORDER BY nome_produto";
$resultado = mysqli_query($conexao, $sql);

if ($resultado) {
    while ($linha = mysqli_fetch_assoc($resultado)) {
        $produtos[] = $linha;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Peças de Computador</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            padding: 20px;
            color: #333;
        }
        
        .container {
            max-width: 1200px;
            margin: 20px auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        h1 {
            color: #2c3e50;
            font-size: 24px;
        }
        
        .btn {
            color: white;
            border: none;
            border-radius: 4px;
            padding: 10px 15px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-add {
            background-color: #27ae60;
        }
        
        .btn-dashboard {
            background-color: #3498db;
        }
        
        .btn-edit {
            background-color: #3498db;
            padding: 5px 10px;
            font-size: 13px;
        }
        
        .btn-delete {
            background-color: #e74c3c;
            padding: 5px 10px;
            font-size: 13px;
        }
        
        .btn:hover {
            opacity: 0.9;
        }
        
        .user-info {
            background-color: #ecf0f1;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 14px;
        }
        
        .mensagem {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            text-align: center;
        }
    
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        th {
            background-color: #3498db;
            color: white;
            padding: 12px 10px;
            text-align: left;
        }
        
        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .acoes {
            display: flex;
            gap: 5px;
        }
        
        .sem-produtos {
            text-align: center;
            padding: 30px;
            color: #7f8c8d;
        }
        
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            color: #7f8c8d;
            font-size: 14px;
        }
    </style>
    <script>
        function confirmarExclusao(nome) {
            return confirm('Tem certeza que deseja excluir o produto "' + nome + '"?');
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Peças de Computador</h1>
            <div>
                <a href="adicionarProdutos.php" class="btn btn-add">Adicionar Produto</a>
                <a href="estoque.php" class="btn btn-dashboard">voltar</a>
            </div>
        </div>
    
        <?php if (isset($mensagem)): ?>
            <div class="mensagem <?= strpos($mensagem, 'sucesso') ? 'sucesso' : 'erro' ?>">
                <?= $mensagem ?>
            </div>
        <?php endif; ?>
           
        <?php if (count($produtos) > 0): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Produto</th>
                        <th>Preço</th>
                        <th>Estado</th>
                        <th>Estoque</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produtos as $produto): 
                        $estoque_class = 'estoque-alto';
                        if ($produto['quantidade_estoque'] < 5) $estoque_class = 'estoque-baixo';
                        else if ($produto['quantidade_estoque'] <= 15) $estoque_class = 'estoque-medio';
                    ?>
                    <tr>
                        <td><?= $produto['id_produto'] ?></td>
                        <td><?= htmlspecialchars($produto['nome_produto']) ?></td>
                        <td>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></td>
                        <td class="<?= $produto['estado'] ?>"><?= ucfirst($produto['estado']) ?></td>
                        <td class="<?= $estoque_class ?>"><?= $produto['quantidade_estoque'] ?> unid.</td>
                        <td>
                            <div class="acoes">
                                <a href="alterarProdutos.php?id_produto=<?= $produto['id_produto'] ?>" 
                                   class="btn btn-edit">Editar</a>
                                <a href="lista_produtos.php?excluir=<?= $produto['id_produto'] ?>" 
                                   class="btn btn-delete"
                                   onclick="return confirmarExclusao('<?= htmlspecialchars($produto['nome_produto']) ?>')">
                                   Excluir
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="sem-produtos">
            Nenhum produto cadastrado no sistema
        </div>
        <?php endif; ?>
        
        <div class="footer">
            Sistema de Gerenciamento de Produtos &copy; <?= date('Y') ?>
        </div>
    </div>
</body>
</html>
<?php
mysqli_close($conexao);
?>