<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include '../config.php'; 

function verifica_campo($texto){
    $texto = trim($texto);
    $texto = stripslashes($texto);
    $texto = htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
    return $texto;
}

$nome = $email = $senha = "";
$erro = false;
$erro_nome = $erro_email = $erro_senha = "";
$error_msg = "";
$success = false;

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die('Problemas ao conectar ao BD: ' . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome  = isset($_POST['nome'])  ? verifica_campo($_POST['nome'])  : '';
    $email = isset($_POST['email']) ? verifica_campo($_POST['email']) : '';
    $senha = isset($_POST['senha']) ? verifica_campo($_POST['senha']) : '';

    if ($nome === '') {
        $erro_nome = "O nome é obrigatório.";
        $erro = true;
    }

    if ($senha === '') {
        $erro_senha = "A senha é obrigatória.";
        $erro = true;
    }

    if ($email === '') {
        $erro_email = "O email é obrigatório.";
        $erro = true;
    } else {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erro_email = "E-mail inválido.";
            $erro = true;
        }
    }

    if (!$erro) {
        $nome_e  = mysqli_real_escape_string($conn, $nome);
        $email_e = mysqli_real_escape_string($conn, $email);
        $senha_e = mysqli_real_escape_string($conn, $senha);

        $sql = "INSERT INTO Jogadores (email_jogadores, nome_jogadores, senha_jogadores)
                VALUES ('{$email_e}', '{$nome_e}', '{$senha_e}')
                ON DUPLICATE KEY UPDATE
                  nome_jogadores = VALUES(nome_jogadores),
                  senha_jogadores = VALUES(senha_jogadores)";

        if (mysqli_query($conn, $sql)) {
            $success = true;
            $nome = $email = $senha = "";
        } else {
            $error_msg = mysqli_error($conn);
            $erro = true;
        }
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>Cadastre-se</title>
  <link rel="stylesheet" href="../css/login.css">
  <style>
    .help-block { color:#b00020; display:block; margin-top:6px; }
    .alert-success { color:#0a7a0a; margin-bottom:10px; }
    .alert-danger { color:#b00020; margin-bottom:10px; }
  </style>
</head>
<body>
  <div id="login">
    <form method="POST" id="form-test" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="card">
      <div class="card-header">
        <?php if ($_SERVER["REQUEST_METHOD"] === "POST" && !$erro && $success): ?>
          <div class="alert alert-success">
            <script>alert("Cadastro realizado com sucesso!");</script>
            <p>Cadastro realizado com sucesso. <a href="login.php">Ir para login</a></p>
          </div>
        <?php elseif ($erro && !empty($error_msg)): ?>
          <div class="alert alert-danger">
            <p>Erro ao cadastrar: <?php echo htmlspecialchars($error_msg); ?></p>
          </div>
        <?php endif; ?>
        <h2>Realize seu cadastro</h2>
      </div>

      <div class="card-content <?php if(!empty($erro_nome)){echo "has-error";}?>">
        <div class="card-content-area">
          <label for="usuario">Nome</label>
          <input type="text" name="nome" value="<?php echo htmlspecialchars($nome); ?>" autocomplete="off">
        </div>
        <?php if (!empty($erro_nome)): ?>
          <span id="erro-nome" class="help-block"><?php echo htmlspecialchars($erro_nome); ?></span>
        <?php else: ?>
          <span id="erro-nome" class="help-block"></span>
        <?php endif; ?>

        <div class="card-content-area <?php if(!empty($erro_senha)){echo "has-error";}?>">
          <label for="password">Senha</label>
          <input type="password" name="senha" value="" autocomplete="off">
        </div>
        <?php if (!empty($erro_senha)): ?>
          <span id="erro-senha" class="help-block"><?php echo htmlspecialchars($erro_senha); ?></span>
        <?php else: ?>
          <span id="erro-senha" class="help-block"></span>
        <?php endif; ?>

        <div class="card-content-area <?php if(!empty($erro_email)){echo "has-error";}?>">
          <label for="email">Email</label>
          <input type="text" name="email" value="<?php echo htmlspecialchars($email); ?>" autocomplete="off">
        </div>
        <?php if (!empty($erro_email)): ?>
          <span id="erro-mail" class="help-block"><?php echo htmlspecialchars($erro_email); ?></span>
        <?php else: ?>
          <span id="erro-mail" class="help-block"></span>
        <?php endif; ?>
      </div>

      <div class="card-footer">
        <button type="submit" class="submit">Cadastre-se</button>
        <a href="login.php" class="fazer_login">Já possui uma conta? Login</a>
      </div>
    </form>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(function(){
      $("#form-test").on("submit", function(e) {
        let valid = true;

        let nome_input = $("input[name='nome']");
        if(!nome_input.val() || !nome_input.val().trim()) 
        {
          $("#erro-nome").html("O nome é obrigatório");
          valid = false;
        } else 
        {
          $("#erro-nome").html("");
        }

        let mail_input = $("input[name='email']");
        if(!mail_input.val() || !mail_input.val().trim()) 
        {
          $("#erro-mail").html("O e-mail é obrigatório");
          valid = false;
        } else {
          $("#erro-mail").html("");
        }

        let senha_input = $("input[name='senha']");
        if(!senha_input.val() || !senha_input.val().trim()) {
          $("#erro-senha").html("A senha é obrigatória");
          valid = false;
        } else {
          $("#erro-senha").html("");
        }

        if (!valid) {
          e.preventDefault();
          return false;
        }
        return true;
      });
    });
  </script>
</body>
</html>
