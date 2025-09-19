<?php
include "conexao.php"; // conexão + variáveis Cloudinary

if(isset($_POST['cadastra'])){
    $nome = mysqli_real_escape_string($conexao, $_POST['nome']);
    $descricao = mysqli_real_escape_string($conexao, $_POST['descricao']);
    $preco = floatval($_POST['preco']);
    $imagem_url = "";

    if(isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0){
        $cfile = new CURLFile($_FILES['imagem']['tmp_name'], $_FILES['imagem']['type'], $_FILES['imagem']['name']);

        $timestamp = time();
        $string_to_sign = "timestamp=$timestamp$api_secret";
        $signature = sha1($string_to_sign);

        $data = [
            'file' => $cfile,
            'timestamp' => $timestamp,
            'api_key' => $api_key,
            'signature' => $signature
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.cloudinary.com/v1_1/$cloud_name/image/upload");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        if($response === false){ die("Erro no cURL: " . curl_error($ch)); }
        curl_close($ch);

        $result = json_decode($response, true);
        if(isset($result['secure_url'])){
            $imagem_url = $result['secure_url'];
        } else {
            die("Erro no upload: " . print_r($result, true));
        }
    }

    if($imagem_url != ""){
        $sql = "INSERT INTO produtos (nome, descricao, preco, imagem_url) VALUES ('$nome', '$descricao', $preco, '$imagem_url')";
        mysqli_query($conexao, $sql) or die("Erro ao inserir: " . mysqli_error($conexao));
    }

    header("Location: mural.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8"/>
<title>Mural de Produtos</title>
<link rel="stylesheet" href="style.css">

<style>
  /* Container geral */
  body {
    background: #2c3e50;
    color: #2c3e50;
    font-family: 'Segoe UI', sans-serif;
    margin: 0;
    padding: 0;
  }

  #main, #geral {
    max-width: 600px;
    margin: 40px auto;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    overflow: hidden;
    color: #2c3e50;
    padding: 20px;
  }

  #header {
    background: #2980b9;
    padding: 20px;
    text-align: center;
    border-radius: 12px 12px 0 0;
  }

  #header h1 {
    color: #fff;
    font-weight: bold;
    margin: 0;
  }

  /* Formulário */
  #formulario_mural {
    padding: 20px 0;
  }

  label {
    display: block;
    margin: 12px 0 6px;
    font-weight: 600;
  }

  input[type="text"],
  input[type="number"],
  textarea,
  input[type="file"] {
    width: 100%;
    padding: 10px 12px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
    box-sizing: border-box;
  }

  textarea {
    resize: vertical;
  }

  .btn {
    background-color: #2980b9;
    color: white;
    border: none;
    padding: 12px;
    width: 100%;
    margin-top: 15px;
    font-weight: bold;
    font-size: 15px;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s;
  }

  .btn:hover {
    background-color: #1c5980;
  }

  /* Lista de produtos */
  .produtos-container {
    margin-top: 30px;
  }

  .produto {
    background: #f7f9fc;
    border-left: 6px solid #2980b9;
    border-radius: 6px;
    padding: 15px;
    margin-bottom: 20px;
    color: #2c3e50;
  }

  .produto p {
    margin: 6px 0;
  }

  /* Centraliza a imagem no produto */
  .produto img {
    display: block;
    margin: 15px auto 0 auto; /* centraliza horizontal */
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  }

  #footer {
    background: #2980b9;
    color: white;
    text-align: center;
    padding: 12px;
    border-radius: 0 0 12px 12px;
    margin-top: 30px;
    font-size: 14px;
  }
</style>
</head>
<body>
<div id="main">
    <div id="geral">
        <div id="header">
            <h1>Mural de Produtos</h1>
        </div>

        <div id="formulario_mural">
            <form id="mural" method="post" enctype="multipart/form-data">
                <label>Nome do produto:</label>
                <input type="text" name="nome" required/>

                <label>Descrição:</label>
                <textarea name="descricao" required></textarea>

                <label>Preço:</label>
                <input type="number" step="0.01" name="preco" required/>

                <label>Imagem:</label>
                <input type="file" name="imagem" accept="image/*" required/>

                <input type="submit" value="Cadastrar Produto" name="cadastra" class="btn"/>
            </form>
        </div>

        <div class="produtos-container">
        <?php
        $seleciona = mysqli_query($conexao, "SELECT * FROM produtos ORDER BY id DESC");
        while($res = mysqli_fetch_assoc($seleciona)){
            echo '<div class="produto">';
            echo '<p><strong>ID:</strong> ' . $res['id'] . '</p>';
            echo '<p><strong>Nome:</strong> ' . htmlspecialchars($res['nome']) . '</p>';
            echo '<p><strong>Preço:</strong> R$ ' . number_format($res['preco'], 2, ',', '.') . '</p>';
            echo '<p><strong>Descrição:</strong> ' . nl2br(htmlspecialchars($res['descricao'])) . '</p>';
            // CORREÇÃO AQUI: aspas corrigidas para echo da imagem
            echo '<img src="' . htmlspecialchars($res['imagem_url']) . '" alt="' . htmlspecialchars($res['nome']) . '">';
            echo '</div>';
        }
        ?>
        </div>

        <div id="footer">
            <p>Mural - Cloudinary & PHP</p>
        </div>
    </div>
</div>
</body>
</html>