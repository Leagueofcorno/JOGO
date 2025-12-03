<?php 
include '../config.php';


$conn = mysqli_connect($servername, $username, $password, $dbname);
if(!$conn){
  die('Problemas ao conectar ao BD: ' . mysqli_connect_error());
}

$sql = "use $dbname";
$query = mysqli_query($conn, $sql);

if(!$query){
  die('Error:' . mysqli_connect_error());
}

$view = $_GET['view'];
$sql = "select Id, nome, pontos from Jogadores where Id = $view";
$query_jogadores = mysqli_query($conn, $sql);

if(!$query_jogadores){
  die('Error:' . mysqli_connect_error());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/assets/favicon.ico" type="image/x-icon">
    <title>Histórico do Jogador</title>
</head>
<body>
    
</body>
</html>