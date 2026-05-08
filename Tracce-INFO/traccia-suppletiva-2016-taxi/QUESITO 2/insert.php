<?php
require "db_connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $idCliente = $_POST["IdCliente"];
    $partenza = $_POST["LuogoPartenza"];
    $arrivo = $_POST["LuogoArrivo"];
    $data = $_POST["DataC"];
    $ora = $_POST["OraA"];

    if (empty($idCliente) || empty($partenza) || empty($arrivo)) {
        die("ID Cliente, luogo di partenza e luogo di arrivo sono obbligatori.");
    }

    if (!is_numeric($idCliente)) {
        die("ID Cliente deve essere numerico.");
    }


    $query = "
        INSERT INTO CORSA (IdCliente, LuogoPartenza, LuogoArrivo, DataC, OraA)
        VALUES (?, ?, ?, ?, ?)
    ";

    $stmt = $connection->prepare($query);

    if (!$stmt) {
        die("Errore nella preparazione della query: " . $connection->error);
    }

    $stmt->bind_param(
        "issss",
        $idCliente,
        $partenza,
        $arrivo,
        $data,
        $ora
    );

    if ($stmt->execute()) {
        echo "<p>Richiesta di trasporto inserita correttamente.</p>";
    } else {
        echo "<p>Errore nell'inserimento: " . $stmt->error . "</p>";
    }

    $stmt->close();
    $connection->close();
}
?>