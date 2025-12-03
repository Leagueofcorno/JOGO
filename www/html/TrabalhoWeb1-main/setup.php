<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'config.php';

$conn = mysqli_connect($servername, $username, $password);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "<br>Conexão estabelecida<br>";

$sql = "CREATE DATABASE IF NOT EXISTS `" . mysqli_real_escape_string($conn, $dbname) . "`";
if (!mysqli_query($conn, $sql)) {
    die("<br>Erro ao criar banco: " . mysqli_error($conn));
}
mysqli_select_db($conn, $dbname);

/* Tabela Jogadores */
$sql = "CREATE TABLE IF NOT EXISTS Jogadores (
  id_jogadores INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email_jogadores VARCHAR(255) NOT NULL UNIQUE,
  nome_jogadores VARCHAR(255) NOT NULL,
  senha_jogadores VARCHAR(255) NOT NULL
)";
if (!mysqli_query($conn, $sql)) {
    die("<br>Erro ao criar Jogadores: " . mysqli_error($conn));
}
echo "<br>Tabela Jogadores pronta<br>";

/* Tabela Elos */
$sql = "CREATE TABLE IF NOT EXISTS Elos (
  id_elos INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome_elos VARCHAR(255) NOT NULL,
  meta INT NOT NULL DEFAULT 0,
  vagas INT NOT NULL DEFAULT 0
)";
if (!mysqli_query($conn, $sql)) {
    die("<br>Erro ao criar Elos: " . mysqli_error($conn));
}
echo "<br>Tabela Elos pronta<br>";

/* Tabela Historico (sem índices explícitos) */
$sql = "CREATE TABLE IF NOT EXISTS Historico (
  id_hist INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  dt_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  idUsuario INT UNSIGNED NULL,
  pontos INT NOT NULL DEFAULT 0,
  idElos INT UNSIGNED NULL,
  tempo INT NOT NULL DEFAULT 0,
  ranking INT NULL,
  CONSTRAINT fk_historico_usuario FOREIGN KEY (idUsuario) REFERENCES Jogadores(id_jogadores) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT fk_historico_elo FOREIGN KEY (idElos) REFERENCES Elos(id_elos) ON DELETE SET NULL ON UPDATE CASCADE
)";
if (!mysqli_query($conn, $sql)) {
    die("<br>Erro ao criar Historico: " . mysqli_error($conn));
}
echo "<br>Tabela Historico pronta<br>";

/* Inserção de Elos de exemplo (coluna nome_elos) */
$sql = "INSERT INTO Elos (nome_elos, meta, vagas) VALUES
    ('Desafiador', 20000, 10),
    ('Grande Mestre', 10000, 15),
    ('Mestre', 9000, 20)
ON DUPLICATE KEY UPDATE nome_elos = VALUES(nome_elos)";
if (!mysqli_query($conn, $sql)) {
    echo "<br>Erro ao inserir elos: " . mysqli_error($conn);
} else {
    echo "<br>Elos de exemplo inseridos/atualizados<br>";
}

/* Usuários de exemplo (sem foreach, sem hash, nomes de colunas consistentes) */
$users = [
    ['gabriel@exemplo.com', 'Gabriel', '123'],
    ['mari@exemplo.com', 'Mariana', '123']
];

$values = [];
for ($i = 0; $i < count($users); $i++) {
    $email = mysqli_real_escape_string($conn, $users[$i][0]);
    $nome  = mysqli_real_escape_string($conn, $users[$i][1]);
    $senha = mysqli_real_escape_string($conn, $users[$i][2]);
    $values[] = "('{$email}', '{$nome}', '{$senha}')";
}

if (!empty($values)) {
    $sqlUser = "INSERT INTO Jogadores (email_jogadores, nome_jogadores, senha_jogadores) VALUES " . implode(',', $values)
             . " ON DUPLICATE KEY UPDATE nome_jogadores = VALUES(nome_jogadores), senha_jogadores = VALUES(senha_jogadores)";
    if (!mysqli_query($conn, $sqlUser)) {
        echo "<br>Erro ao inserir/atualizar usuários: " . mysqli_error($conn);
    } else {
        echo "<br>Usuários de exemplo inseridos/atualizados<br>";
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Setup concluído</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    .btn { display:inline-block; padding:10px 16px; background:#2d89ef; color:#fff; text-decoration:none; border-radius:4px; }
    .btn:hover { background:#1b6fd8; }
    .status { margin-bottom:16px; }
  </style>
</head>
<body>
    <p class="status">Setup concluído.</p>
    <a class="btn" href="/TrabalhoWeb1-main/php/login.php">Ir para Login</a>
</body>
</html>
