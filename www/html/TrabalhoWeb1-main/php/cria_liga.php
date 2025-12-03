<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="cria_liga.css">
    <title>Gestão de Ligas</title>
</head>
<body>
    <h1>Gestão de Ligas</h1>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["criar_liga"])) 
        {
            $nome = htmlspecialchars($_POST["nome"]);
            $plv_chave = htmlspecialchars($_POST["plv_chave"]);

            echo "<h2>Liga Criada com Sucesso:</h2>";
            echo "<p><strong>Nome da Liga:</strong> $nome</p>";
            echo "<p><strong>Palavra Chave:</strong> $plv_chave</p>";
        }

        if (isset($_POST["entrar_liga"])) 
        {
            $nome_jogador = htmlspecialchars($_POST["nome_jogador"]);
            $codigo_liga = htmlspecialchars($_POST["codigo_liga"]);

            echo "<h2>Você entrou na Liga com sucesso:</h2>";
            echo "<p><strong>Nome do Jogador:</strong> $nome_jogador</p>";
            echo "<p><strong>Código da Liga:</strong> $codigo_liga</p>";
        }
    }
    ?>

    <h2>Criar Liga</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="cria_liga">
        <label for="nome">Nome da Liga:</label>
        <input type="text" id="nome" name="nome" required>

        <label for="plv_chave">Palavra Chave:</label>
        <input type="number" id="plv_chave" name="plv_chave" required>

        <button type="submit" name="criar_liga" class="btn btn-sm animated-button thar-three">Criar Liga</button>
    </form>

    <h2>Entrar em Liga</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="nome_jogador">Nome do Jogador:</label>
        <input type="text" id="nome_jogador" name="nome_jogador" required>

        <label for="codigo_liga">Código da Liga:</label>
        <input type="text" id="codigo_liga" name="codigo_liga" required>

        <button type="submit" name="entrar_liga" class="btn btn-sm animated-button thar-three">Entrar na Liga</button>
    </form>
</body>
</html>
