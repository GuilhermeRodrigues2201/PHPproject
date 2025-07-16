<?php
session_start();

// dashboard.php (corrigir)
if (!isset($_SESSION['usuario'])) { // Verificar 'usuario' em vez de 'gerente'
    header("Location: ../index.php");
    exit();
}

if ($_SESSION['usuario']['tipo'] !== 'gerente') {
    header("Location: ../funcionario/perfil.php");
    exit();
}

// Dados do usuário logado
$usuario = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gerente</title>
   <style>
        /* Estilos adicionais para o dashboard */
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #ddd;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #1e5799;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 20px;
        }
        
        .logout-btn {
            background: linear-gradient(to right, #c62828, #e53935);
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 15px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .logout-btn:hover {
            background: linear-gradient(to right, #b71c1c, #c62828);
            transform: translateY(-2px);
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-value {
            font-size: 36px;
            font-weight: bold;
            color: #1e5799;
            margin: 10px 0;
        }
        
        .stat-label {
            color: #777;
            font-size: 14px;
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .action-btn {
            background: linear-gradient(to right, #1e5799, #2989d8);
            color: white;
            border: none;
            border-radius: 5px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }
        
        .action-btn:hover {
            background: linear-gradient(to right, #163e6f, #1e5799);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
          .action-btn a {
            text-decoration: none;
            color: white;
        }
        
        
        .recent-activity {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .activity-item {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <h1>Painel do Gerente</h1>
            <div class="user-info">
                <div class="user-avatar"><?= strtoupper(substr($usuario['nickname'], 0, 1)) ?></div>
                <div>
                    <div><?= $usuario['nomeCompleto'] ?? $usuario['nickname'] ?></div>
                    <small>Gerente</small>
                </div>
                <form action="../logout.php" method="post">
                    <button type="submit" class="logout-btn">Sair</button>
                </form>
            </div>
        </div>
        <h2>Ações Rápidas</h2>
        <div class="quick-actions">
            <button class="action-btn"><a href="adicionar.php">Adicionar Funcionário</a></button>
            <button class="action-btn"><a href="alterar.php">Alterar informações</a></button>
            <button class="action-btn"><a href="../repositor/listaProdutos.php">Controle de Estoque</a></button>
            <button class="action-btn"><a href="listaFuncionarios.php">Lista Funcionários</a></button>
        </div>
        
</body>
</html>