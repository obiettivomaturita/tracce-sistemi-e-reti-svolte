<?php
require "db_connection.php";

$codP = isset($_GET["codP"]) ? $_GET["codP"] : 0;


if ($codP <= 0) {
    die("Codice POI mancante (QR non valido).");
}


if (!isset($_POST["passwordB"])) {
    header("Location: login.html");
    exit;
}

$passwordB = $_POST["passwordB"];
if (empty($passwordB)) {
    die("Password non inserita.");
}


$query = "SELECT CodB, TipoTariffa
          FROM BIGLIETTO
          WHERE PasswordB = ?
            AND DataValidita = CURDATE()";

$stmt = $connection->prepare($query);
if (!$stmt) die("Errore prepare: " . $connection->error);

$stmt->bind_param("s", $passwordB);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Biglietto non valido o non valido per la data odierna.");
}

$row = $result->fetch_assoc();
$codB =$row["CodB"];


$queryPoi = "SELECT CodP FROM POI WHERE CodP = ?";
$stmtPoi = $connection->prepare($queryPoi);
if (!$stmtPoi) die("Errore prepare: " . $connection->error);

$stmtPoi->bind_param("i", $codP);
$stmtPoi->execute();
$resPoi = $stmtPoi->get_result();

if ($resPoi->num_rows == 0) {
    die("POI inesistente.");
}


$dataAccesso = date("Y-m-d");
$oraI = date("H:i:s");
$oraF = date("H:i:s", time() + 600);

$queryIns = "INSERT INTO ACCESSO (CodB, DataAccesso, OraI, OraF, CodP)
             VALUES (?, ?, ?, ?, ?)";

$stmtIns = $connection->prepare($queryIns);
if (!$stmtIns) die("Errore prepare: " . $connection->error);

$stmtIns->bind_param("isssi", $codB, $dataAccesso, $oraI, $oraF, $codP);

if (!$stmtIns->execute()) {
    die("Errore INSERT accesso: " . $stmtIns->error);
}

$idAcc = $connection->insert_id;

$stmt->close();
$stmtPoi->close();
$stmtIns->close();
$connection->close();

header("Location: pagina_base.php?idAcc=".$idAcc);
?>

