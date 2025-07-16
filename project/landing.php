<?php
session_start();

// Verificação de autenticação
if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit();
}

// Controle de acesso baseado no tipo
$tipo = $_SESSION["usuario"]['tipo'];

switch ($tipo) {
    case 'gerente':
        echo "Bem-vindo, Gerente!";
        <p>bill admin</p>
        break;
        
    case 'repositor':
        echo "Bem-vindo, Repositor!";
        <p>bill repositor</p>
        break;
        
    case 'funcionario':
        echo "Bem-vindo, Funcionário!";
        <p>bill funcionario</p>
        break;
}
?>