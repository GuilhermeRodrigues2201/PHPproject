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

// Processar exclusão de funcionário
if (isset($_GET['excluir'])) {
    $id = (int) $_GET['excluir'];
    
    // Não permitir excluir a si mesmo
    if ($id == $_SESSION['usuario']['id']) {
        $mensagem = "<p class='erro'>Você não pode excluir seu próprio usuário!</p>";
    } else {
        $sql = "DELETE FROM funcionarios WHERE idFunc = $id";
        
        if (mysqli_query($conexao, $sql)) {
            $mensagem = "<p class='sucesso'>Funcionário excluído com sucesso!</p>";
        } else {
            $mensagem = "<p class='erro'>Erro ao excluir: " . mysqli_error($conexao) . "</p>";
        }
    }
}

// Buscar todos os funcionários
$funcionarios = [];
$sql = "SELECT idFunc, nickname, nomeCompleto, email, tipo FROM funcionarios ORDER BY nomeCompleto";
$resultado = mysqli_query($conexao, $sql);

if ($resultado) {
    while ($linha = mysqli_fetch_assoc($resultado)) {
        $funcionarios[] = $linha;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Funcionários</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .management-container {
            max-width: 1200px;
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
        }
        
        .btn-azul:hover {
            background: linear-gradient(to right, #163e6f, #1e5799);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .btn-vermelho {
            background: linear-gradient(to right, #e74c3c, #c0392b);
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 15px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-vermelho:hover {
            background: linear-gradient(to right, #c0392b, #e74c3c);
            transform: translateY(-2px);
        }
        
        .funcionarios-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .funcionarios-table th {
            background: linear-gradient(to right, #1e5799, #2989d8);
            color: white;
            padding: 15px;
            text-align: left;
        }
        
        .funcionarios-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        
        .funcionarios-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .funcionarios-table tr:hover {
            background-color: #f0f8ff;
        }
        
        .tipo-gerente { color: #1e5799; font-weight: 600; }
        .tipo-repositor { color: #2989d8; font-weight: 600; }
        .tipo-funcionario { color: #555; }
        
        .acoes-cell {
            text-align: center;
            min-width: 150px;
        }
        
        .btn-container {
            display: flex;
            gap: 10px;
            justify-content: center;
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
        
        .sem-registros {
            text-align: center;
            padding: 30px;
            color: #777;
            font-size: 18px;
        }
        
        .search-container {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }
        
        .search-container input {
            flex: 1;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 25px;
            gap: 5px;
        }
        
        .pagination a, .pagination span {
            display: inline-block;
            padding: 8px 15px;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-decoration: none;
            color: #1e5799;
        }
        
        .pagination a:hover {
            background-color: #1e5799;
            color: white;
        }
        
        .pagination .current {
            background-color: #1e5799;
            color: white;
            font-weight: bold;
        }
    </style>
    <script>
        function confirmarExclusao(nome) {
            return confirm(`Tem certeza que deseja excluir o funcionário "${nome}"?\nEsta ação não pode ser desfeita.`);
        }
    </script>
</head>
<body>
    <div class="management-container">
        <div class="header-section">
            <h1>Lista de Funcionários</h1>
            <a href="adicionar.php" class="btn-azul">Adicionar Novo</a>
        </div>
        
        <?= isset($mensagem) ? $mensagem : '' ?>
        
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Pesquisar funcionários...">
        </div>
        
        <?php if (count($funcionarios) > 0): ?>
        <div class="table-responsive">
            <table class="funcionarios-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome Completo</th>
                        <th>Nickname</th>
                        <th>Email</th>
                        <th>Tipo</th>
                        <th class="acoes-cell">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($funcionarios as $funcionario): ?>
                    <tr>
                        <td><?= $funcionario['idFunc'] ?></td>
                        <td><?= htmlspecialchars($funcionario['nomeCompleto']) ?></td>
                        <td><?= htmlspecialchars($funcionario['nickname']) ?></td>
                        <td><?= htmlspecialchars($funcionario['email']) ?></td>
                        <td>
                            <?php
                            $classeTipo = 'tipo-' . $funcionario['tipo'];
                            $tipoFormatado = ucfirst($funcionario['tipo']);
                            echo "<span class='$classeTipo'>$tipoFormatado</span>";
                            ?>
                        </td>
                        <td class="acoes-cell">
                            <div class="btn-container">
                                <a href="alterar.php?id_funcionario=<?= $funcionario['idFunc'] ?>" 
                                   class="btn-azul">Editar</a>
                                <a href="lista_funcionarios.php?excluir=<?= $funcionario['idFunc'] ?>" 
                                   class="btn-vermelho"
                                   onclick="return confirmarExclusao('<?= htmlspecialchars($funcionario['nomeCompleto']) ?>')">
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
        <div class="sem-registros">
            Nenhum funcionário cadastrado no sistema
        </div>
        <?php endif; ?>
        
        <div class="btn-container" style="margin-top: 30px;">
            <a href="dashboard.php" class="btn-azul">Voltar ao Dashboard</a>
        </div>
    </div>
    
    <script>
        // Filtro de pesquisa em tempo real
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchText = this.value.toLowerCase();
            const rows = document.querySelectorAll('.funcionarios-table tbody tr');
            
            rows.forEach(row => {
                const nome = row.cells[1].textContent.toLowerCase();
                const nickname = row.cells[2].textContent.toLowerCase();
                const email = row.cells[3].textContent.toLowerCase();
                
                if (nome.includes(searchText) || nickname.includes(searchText) || email.includes(searchText)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
<?php
mysqli_close($conexao);
?>