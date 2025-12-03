<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require "../config.php";
require "autenticacao.php";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die('Problemas ao conectar ao BD: ' . mysqli_connect_error());
}

$error = false;
$senha = $email = "";
$error_msg = "";

if ((!isset($login) || !$login) && $_SERVER["REQUEST_METHOD"] === "POST") {

    $email = isset($_POST["email"]) ? mysqli_real_escape_string($conn, trim($_POST["email"])) : '';
    $senha = isset($_POST["senha"]) ? mysqli_real_escape_string($conn, $_POST["senha"]) : '';

    $sql = "SELECT id_jogadores, nome_jogadores, email_jogadores, senha_jogadores
            FROM Jogadores
            WHERE email_jogadores = '$email'
            LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            if ($user["senha_jogadores"] === $senha) {
                $_SESSION["user_id"]    = (int)$user["id_jogadores"];
                $_SESSION["user_name"]  = $user["nome_jogadores"];
                $_SESSION["user_email"] = $user["email_jogadores"];

                header("Location: ../index.php");
                exit();
            } else {
                $error_msg = "Senha incorreta!";
                $error = true;
            }
        } else {
            $error_msg = "Usuário não encontrado!";
            $error = true;
        }
    } else {
        $error_msg = mysqli_error($conn);
        $error = true;
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/login.css">
    <title>Login</title>
</head>
<body>
<?php if (!empty($login) && $login): ?>
    <div class="loginfeito">
        <h1><strong>OOPS!</strong></h1>
        <h3><strong>Você já está logado!</strong></h3>
        <form action="../index.php">
            <div class="card-footer">
                <button type="submit" class="submitxd">Ir para a página inicial</button>
            </div>
        </form>
    </div>
    <?php exit(); ?>
<?php endif; ?>

<div id="login">
    <form method="POST" action="login.php" class="card">
        <div class="card-header">
            <h2>Bem vindo(a) de volta!</h2>
        </div>
        <div class="card-content">
            <?php if ($error): ?>
                <h3 class="erro"><?php echo htmlspecialchars($error_msg); ?></h3>
            <?php endif; ?>
            <div class="card-content-area">
                <label for="usuario">Email</label>
                <input type="text" name="email" autocomplete="off" value="<?php echo htmlspecialchars($email); ?>">
            </div>
            <div class="card-content-area">
                <label for="password">Senha</label>
                <input type="password" name="senha" autocomplete="off">
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="submit">Login</button>
            <a href="cadastro.php" class="ir_cadastrar">Não possui uma conta? Cadastre-se</a>
        </div>
    </form>
</div>
</body>
</html>
