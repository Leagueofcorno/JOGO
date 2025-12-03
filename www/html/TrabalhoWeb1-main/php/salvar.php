<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado.']);
    exit;
}

require '../config.php'; 

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos.']);
    exit;
}

$pontos = isset($input['pontos']) ? (int)$input['pontos'] : 0;
$tempo  = isset($input['tempo'])  ? (int)$input['tempo']  : 0;
$maybeIdElos = isset($input['idElos']) ? (int)$input['idElos'] : null;

$userId = (int)$_SESSION['user_id'];

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Erro ao conectar ao banco.']);
    exit;
}


$insertSql = "INSERT INTO Historico (idUsuario, pontos, idElos, tempo, ranking) VALUES (?, ?, NULL, ?, NULL)";
$stmt = mysqli_prepare($conn, $insertSql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Erro na preparação da inserção.']);
    mysqli_close($conn);
    exit;
}
mysqli_stmt_bind_param($stmt, 'iii', $userId, $pontos, $tempo);
$exec = mysqli_stmt_execute($stmt);
if (!$exec) {
    $err = mysqli_error($conn);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar histórico: ' . $err]);
    exit;
}
$insertedId = mysqli_insert_id($conn);
mysqli_stmt_close($stmt);


$elosSql = "SELECT id_elos, nome_elos, meta, vagas FROM Elos ORDER BY meta DESC";
$resElos = mysqli_query($conn, $elosSql);
if (!$resElos) {
    echo json_encode(['success' => true, 'message' => 'Registro salvo, mas falha ao buscar elos.', 'id_hist' => $insertedId]);
    mysqli_close($conn);
    exit;
}

$assignedElo = null;

while ($elo = mysqli_fetch_assoc($resElos)) {
    $idElo = (int)$elo['id_elos'];
    $meta  = (int)$elo['meta'];
    $vagas = (int)$elo['vagas'];

    if ($pontos < $meta) {
        continue;
    }

    $countSql = "
      SELECT COUNT(*) AS ocupacao FROM (
        SELECT h.idUsuario, h.idElos
        FROM Historico h
        INNER JOIN (
          SELECT idUsuario, MAX(id_hist) AS max_hist
          FROM Historico
          GROUP BY idUsuario
        ) latest ON h.idUsuario = latest.idUsuario AND h.id_hist = latest.max_hist
        WHERE h.idElos = ?
      ) t
    ";
    $stmtCount = mysqli_prepare($conn, $countSql);
    if (!$stmtCount) {
        continue;
    }
    mysqli_stmt_bind_param($stmtCount, 'i', $idElo);
    mysqli_stmt_execute($stmtCount);
    $resCount = mysqli_stmt_get_result($stmtCount);
    $rowCount = mysqli_fetch_assoc($resCount);
    $ocupacao = isset($rowCount['ocupacao']) ? (int)$rowCount['ocupacao'] : 0;
    mysqli_stmt_close($stmtCount);

    if ($ocupacao < $vagas) {
        $updateSql = "UPDATE Historico SET idElos = ? WHERE id_hist = ?";
        $stmtUpd = mysqli_prepare($conn, $updateSql);
        if ($stmtUpd) {
            mysqli_stmt_bind_param($stmtUpd, 'ii', $idElo, $insertedId);
            $okUpd = mysqli_stmt_execute($stmtUpd);
            mysqli_stmt_close($stmtUpd);
            if ($okUpd) {
                $assignedElo = [
                    'id_elos' => $idElo,
                    'nome_elos' => $elo['nome_elos'],
                    'meta' => $meta,
                    'vagas' => $vagas,
                    'ocupacao' => $ocupacao + 1 
                ];
            }
        }
        break; 
    }
}


mysqli_close($conn);

$response = ['success' => true, 'message' => 'Registro salvo.', 'id_hist' => $insertedId];
if ($assignedElo) {
    $response['assigned_elo'] = $assignedElo;
} else {
    $response['assigned_elo'] = null;
    $response['note'] = 'Nenhum elo com vaga foi atribuído (ou jogador não atingiu metas).';
}

echo json_encode($response);
