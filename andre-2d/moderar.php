<?php
include "conexao.php";
if(isset($_POST['atualiza'])){
    $idatualiza = intval($_POST['id']);
    $nome       = mysqli_real_escape_string($conexao, $_POST['nome']);
    $email      = mysqli_real_escape_string($conexao, $_POST['email']);
    $msg        = mysqli_real_escape_string($conexao, $_POST['msg']);

    $sql = "UPDATE andre_proj SET nome='$nome', email='$email', mensagem='$msg' WHERE id=$idatualiza";
    mysqli_query($conexao, $sql) or die("Erro ao atualizar: " . mysqli_error($conexao));
    header("Location: moderar.php");
    exit;
}
if(isset($_GET['acao']) && $_GET['acao'] == 'excluir'){
    $id = intval($_GET['id']);
    mysqli_query($conexao, "DELETE FROM andre_proj WHERE id=$id") or die("Erro ao deletar: " . mysqli_error($conexao));
    header("Location: moderar.php");
    exit;
}
$editar_id = isset($_GET['acao']) && $_GET['acao'] == 'editar' ? intval($_GET['id']) : 0;
$andre_proj_editar = null;
if($editar_id){
    $res = mysqli_query($conexao, "SELECT * FROM andre_proj WHERE id=$editar_id");
    $andre_proj_editar = mysqli_fetch_assoc($res);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8"/>
<title>Moderar pedidos</title>
<link rel="stylesheet" href="style.css"/>
</head>
<body>
<div id="main">
<div id="geral">
<div id="header">
    <h1>Mural de pedidos</h1>
</div>
<?php if($andre_proj_editar): ?>
<div id="formulario_mural">
<form method="post">
    <label>Nome:</label>
    <input type="text" name="nome" value="<?php echo htmlspecialchars($andre_proj_editar['nome']); ?>"/><br/>
    <label>Email:</label>
    <input type="text" name="email" value="<?php echo htmlspecialchars($andre_proj_editar['email']); ?>"/><br/>
    <label>Mensagem:</label>
    <textarea name="msg"><?php echo htmlspecialchars($andre_proj_editar['mensagem']); ?></textarea><br/>
    <input type="hidden" name="id" value="<?php echo $andre_proj_editar['id']; ?>"/>
    <input type="submit" name="atualiza" value="Modificar andre_proj" class="btn"/>
</form>
</div>
<?php endif; ?>
<?php
$seleciona = mysqli_query($conexao, "SELECT * FROM andre_proj ORDER BY id DESC");
if(mysqli_num_rows($seleciona) <= 0){
    echo "<p>Nenhum pedido no mural!</p>";
}else{
    while($res = mysqli_fetch_assoc($seleciona)){
        echo '<ul class="andre_proj">';
        echo '<li><strong>ID:</strong> ' . $res['id'] . ' |
              <a href="moderar.php?acao=excluir&id=' . $res['id'] . '">Remover</a> |
              <a href="moderar.php?acao=editar&id=' . $res['id'] . '">Modificar</a></li>';
        echo '<li><strong>Nome:</strong> ' . htmlspecialchars($res['nome']) . '</li>';
        echo '<li><strong>Email:</strong> ' . htmlspecialchars($res['email']) . '</li>';
        echo '<li><strong>Mensagem:</strong> ' . nl2br(htmlspecialchars($res['mensagem'])) . '</li>';
        echo '</ul>';
    }
}
?>
<div id="footer">
</div>
</div>
</div>
</body>
</html>