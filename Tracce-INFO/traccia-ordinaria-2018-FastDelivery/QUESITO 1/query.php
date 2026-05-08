<?php
require_once "db_connection.php"; 

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo "Richiesta non valida";
    exit;
}

$email = $_POST["Email"];
$password = $_POST["PasswordU"];
$codP = $_POST["CodP"];


$sqlU = "SELECT CF, PasswordU, Nome, Cognome
         FROM UTENTE
         WHERE Email = ?";

$stmtU = $conn->prepare($sqlU);
$stmtU->bind_param("s", $email);
$stmtU->execute();
$resU = $stmtU->get_result();

if ($resU->num_rows != 1) {
    echo "Credenziali non valide";
    exit;
}

$utente = $resU->fetch_assoc();

if (!password_verify($password, $utente["PasswordU"])) {
    echo "Credenziali non valide";
    exit;
}

$cf = $utente["CF"];

$sqlP = "SELECT CodP, Tipo, DataSpeidzione
         FROM PACCO
         WHERE CodP = ? AND CF = ?";

$stmtP = $conn->prepare($sqlP);
$stmtP->bind_param("ss", $codP, $cf);
$stmtP->execute();
$resP = $stmtP->get_result();

if ($resP->num_rows != 1) {
    echo "Pacco non trovato o non associato all'utente";
    exit;
}

$pacco = $resP->fetch_assoc();


$sqlT = "
SELECT t.DataT, t.OraT, t.TipoEvento, t.Annotazioni,
       n.CodSede, n.Tipo, n.Indirizzo
FROM TRACCIAMENTO t
JOIN NODO_LOGISTICO n ON n.CodSede = t.CodSede
WHERE t.CodP = ?
ORDER BY t.DataT ASC, t.OraT ASC
";

$stmtT = $conn->prepare($sqlT);
$stmtT->bind_param("s", $codP);
$stmtT->execute();
$resT = $stmtT->get_result();

if ($resT->num_rows == 0) {
    echo "Nessun evento di tracciamento disponibile";
    exit;
}

$eventi = [];
$consegnato = false;
$ultimo = null;

while ($row = $resT->fetch_assoc()) {
    $eventi[] = $row;
    $ultimo = $row;
    if (strtoupper($row["TipoEvento"]) == "CONSEGNA") {
        $consegnato = true;
    }
}


if ($consegnato) {
    echo "<p><b>Stato attuale:</b> CONSEGNATO</p>";
} else {
    echo "<p><b>Stato attuale:</b> IN TRANSITO presso " .
         $ultimo["Tipo"] . " " . $ultimo["CodSede"] .
         " - " . $ultimo["DataT"] . " " . $ultimo["OraT"] .
         "</p>";
}


echo "<table border='1' cellpadding='5'>";
echo "<tr>
        <th>Data</th>
        <th>Ora</th>
        <th>Evento</th>
        <th>Nodo</th>
        <th>Tipo nodo</th>
        <th>Indirizzo</th>
        <th>Annotazioni</th>
      </tr>";

foreach ($eventi as $e) {
    echo "<tr>";
    echo "<td>" . $e["DataT"] . "</td>";
    echo "<td>" . $e["OraT"] . "</td>";
    echo "<td>" . $e["TipoEvento"] . "</td>";
    echo "<td>" . $e["CodSede"] . "</td>";
    echo "<td>" . $e["Tipo"] . "</td>";
    echo "<td>" . $e["Indirizzo"] . "</td>";
    echo "<td>" . $e["Annotazioni"] . "</td>";
    echo "</tr>";
}

echo "</table>";

$stmtU->close();
$stmtP->close();
$stmtT->close();
$conn->close();
?>