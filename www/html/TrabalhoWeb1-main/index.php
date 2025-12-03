<?php
require "php/autenticacao.php";

if (!$login) {
    header("Location: php/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Hub inicial</title>
    <link rel="stylesheet" href="css/menu.css">
</head>
<body>
    <main class="center-hub">
        <div class="card">
            <h1 class="title">Hub inicial</h1>
            <p class="subtitle">Escolha uma opção para começar.</p>

            <div class="actions">
                <a class="btn primary" href="php/teste.html">Jogar</a>
                <a class="btn" href="records.php">Records</a>
                <a class="btn" href="como-jogar.php">Como jogar</a>
            </div>

            <div class="user-info">
                <span class="username">Olá, <?php echo htmlspecialchars($user_name ?? "Jogador"); ?></span>
            </div>

            <div class="logout-area">
                <a class="btn logout" href="php/logout.php">Deslogar</a>
            </div>
        </div>
    </main>
</body>
</html>
