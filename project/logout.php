<?php
// logout.php
session_start();
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e5799, #207cca, #2989d8, #1e5799);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            text-align: center;
        }
        
        .logout-box {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            padding: 40px;
            max-width: 400px;
            width: 90%;
        }
        
        h1 {
            color: #1e5799;
            margin-bottom: 25px;
        }
        
        p {
            color: #555;
            font-size: 18px;
            margin-bottom: 30px;
        }
        
        .btn {
            background: linear-gradient(to right, #1e5799, #2989d8);
            color: white;
            border: none;
            border-radius: 5px;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn:hover {
            background: linear-gradient(to right, #163e6f, #1e5799);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="logout-box">
        <h1>Sessão Encerrada</h1>
        <p>Você foi desconectado do sistema.</p>
        <a href="index.php" class="btn">Voltar à Página Inicial</a>
        <link rel="stylesheet" href="../css/estilos.css">
    </div>
</body>
</html>