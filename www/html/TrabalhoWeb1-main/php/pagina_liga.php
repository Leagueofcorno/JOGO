<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página da Liga</title>
</head>
<body>
    <h1>Página da Liga</h1>

    <?php
    require_once "config.php";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexão com o banco de dados falhou: " . $conn->connect_error);
    }

    $sql = "SELECT nome, pontuacao_total, pontuacao_semanal FROM jogadores";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
    ?>
        <section>
            <h3>Todos os Jogadores</h3>
            <ul>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <li><?php echo $row['nome']; ?></li>
                <?php endwhile; ?>
            </ul>
        </section>

        <section>
            <h3>Pontuação Total</h3>
            <ul>
                <?php
                $result->data_seek(0);
                while ($row = $result->fetch_assoc()) :
                ?>
                    <li><?php echo "{$row['nome']}: {$row['pontuacao_total']} pontos"; ?></li>
                <?php endwhile; ?>
            </ul>
        </section>

        <section>
            <h3>Pontuação Semanal</h3>
            <ul>
                <?php
                $result->data_seek(0);
                while ($row = $result->fetch_assoc()) :
                ?>
                    <li><?php echo "{$row['nome']}: {$row['pontuacao_semanal']} pontos"; ?></li>
                <?php endwhile; ?>
            </ul>
        </section>
    <?php
    } else {
        echo "Não há jogadores na liga.";
    }
    ?>
</body>
</html>
