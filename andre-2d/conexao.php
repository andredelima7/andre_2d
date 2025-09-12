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

$cloud_name = "deybunoxn";
$api_key    = "998575748394411";
$api_secret = "GlZ_EE9vUaQDM6WHw6-55nivknE


"; 

?>