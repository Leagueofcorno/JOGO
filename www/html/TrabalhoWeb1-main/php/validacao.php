<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'config.php'; 

function verifica_campo($texto){
  $texto = trim($texto);
  $texto = stripslashes($texto);
  $texto = htmlspecialchars($texto);
  return $texto;
}

$nome = $email = $senha = "";
$erro = false;
$mensagens = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  if (empty($_POST["nome"])) {
    $mensagens['nome'] = "O nome é obrigatório.";
    $erro = true;
  } else {
    $nome = verifica_campo($_POST["nome"]);
  }

  if (empty($_POST["senha"])) {
    $mensagens['senha'] = "A senha é obrigatória.";
    $erro = true;
  } else {
    $senha = verifica_campo($_POST["senha"]);
  }

  if (empty($_POST["email"])) {
    $mensagens['email'] = "O email é obrigatório.";
    $erro = true;
  } else {
    $email = verifica_campo($_POST["email"]);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $mensagens['email'] = "$email é um e-mail inválido.";
      $erro = true;
    }
  }

  if (!$erro) {
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    if (!$conn) 
      {
      $mensagens['db'] = "Erro de conexão: " . mysqli_connect_error();
    } else 
    {
      $email_e = mysqli_real_escape_string($conn, $email);
      $nome_e  = mysqli_real_escape_string($conn, $nome);
      $senha_e = mysqli_real_escape_string($conn, $senha);

      $sql = "INSERT INTO Jogadores (email_func, nome_func, senha_func)
              VALUES ('{$email_e}', '{$nome_e}', '{$senha_e}')
              ON DUPLICATE KEY UPDATE
                nome_func = VALUES(nome_func),
                senha_func = VALUES(senha_func)";

      if (mysqli_query($conn, $sql)) {
        $mensagens['success'] = "Registro realizado com sucesso.";
      } else {
        $mensagens['db'] = "Erro ao inserir usuário: " . mysqli_error($conn);
      }

      mysqli_close($conn);
    }
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Registro</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; max-width:600px; }
    label { display:block; margin-top:10px; }
    input[type="text"], input[type="email"], input[type="password"] { width:100%; padding:8px; box-sizing:border-box; }
    .error { color:#b00020; margin-top:6px; }
    .success { color:#0a7a0a; margin-top:6px; }
    .btn { margin-top:12px; padding:10px 14px; background:#2d89ef; color:#fff; border:none; cursor:pointer; border-radius:4px; }
    .btn:hover { background:#1b6fd8; }
  </style>
</head>
<body>
  <h2>Registrar usuário</h2>

  <?php if (!empty($mensagens['db'])): ?>
    <p class="error"><?= htmlspecialchars($mensagens['db']) ?></p>
  <?php endif; ?>

  <?php if (!empty($mensagens['success'])): ?>
    <p class="success"><?= htmlspecialchars($mensagens['success']) ?></p>
    <p><a href="/TrabalhoWeb1-main/php/login.php">Ir para Login</a></p>
  <?php endif; ?>

  <form method="post" action="">
    <label for="nome">Nome</label>
    <input id="nome" name="nome" type="text" value="<?= htmlspecialchars($nome) ?>">
    <?php if (!empty($mensagens['nome'])): ?><div class="error"><?= htmlspecialchars($mensagens['nome']) ?></div><?php endif; ?>

    <label for="email">Email</label>
    <input id="email" name="email" type="email" value="<?= htmlspecialchars($email) ?>">
    <?php if (!empty($mensagens['email'])): ?><div class="error"><?= htmlspecialchars($mensagens['email']) ?></div><?php endif; ?>

    <label for="senha">Senha</label>
    <input id="senha" name="senha" type="password" value="">
    <?php if (!empty($mensagens['senha'])): ?><div class="error"><?= htmlspecialchars($mensagens['senha']) ?></div><?php endif; ?>

    <button class="btn" type="submit">Registrar</button>
  </form>
</body>
</html>
