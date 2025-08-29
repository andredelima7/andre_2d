<?php
// Configurações do banco
$host     = "localhost";
$usuario  = "root";
$senha    = "";
$banco    = "projeto_andre";

// Conexão MySQLi
$conexao = mysqli_connect($host, $usuario, $senha, $banco);

if (!$conexao) {
    die("Erro ao conectar: " . mysqli_connect_error());
}

// Ajustar charset para suportar acentos
mysqli_set_charset($conexao, "utf8");
?>