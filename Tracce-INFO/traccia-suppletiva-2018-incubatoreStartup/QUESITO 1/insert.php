<?php
require "db_connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cods = $_POST["CodS"];
    $nome = $_POST["Nome"];
    $settore = $_POST["Settore"];
    $indirizzo = $_POST["Indirizzo"];
    $ragione = $_POST["RagioneSociale"];
    $datac = $_POST["DataC"];
    $orac = $_POST["OraC"];
    $descrizione = $_POST["Descrizione"];
    $allegato = $_POST["Allegato"];
    $stato = $_POST["Stato"];
    $matricola = $_POST["Matricola"];

    if (empty($cods) || empty($nome) || empty($settore) || empty($indirizzo) || empty($ragione) ||
        empty($datac) || empty($orac) || empty($descrizione) || empty($stato)) {
        die("Tutti i campi obbligatori devono essere compilati.");
    }

    if (!is_numeric($cods)) {
        die("CodS deve essere numerico.");
    }

    if (!empty($matricola) && !is_numeric($matricola)) {
        die("Matricola deve essere numerica.");
    }

    $qStartup = "INSERT INTO STARTUP (CodS, Nome, Settore, Indirizzo, RagioneSociale) VALUES (?, ?, ?, ?, ?)";
    $stmtS = $connection->prepare($qStartup);

    if (!$stmtS) {
        die("Errore nella preparazione query STARTUP: " . $connection->error);
    }

    $stmtS->bind_param("issss", $cods, $nome, $settore, $indirizzo, $ragione);
    $stmtS->close();

    if (empty($matricola)) {
        $qCand = "INSERT INTO CANDIDATURA (DataC, OraC, Descrizione, Allegato, Stato, CodS, Matricola)
                 VALUES (?, ?, ?, ?, ?, ?, NULL)";
        $stmtC = $connection->prepare($qCand);

        if (!$stmtC) {
            die("Errore nella preparazione query CANDIDATURA: " . $connection->error);
        }

        $stmtC->bind_param("sssssi", $datac, $orac, $descrizione, $allegato, $stato, $cods);

    } else {
        $qCand = "INSERT INTO CANDIDATURA (DataC, OraC, Descrizione, Allegato, Stato, CodS, Matricola)
                 VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmtC = $connection->prepare($qCand);

        if (!$stmtC) {
            die("Errore nella preparazione query CANDIDATURA: " . $connection->error);
        }

        $matricola_int = intval($matricola);
        $stmtC->bind_param("sssssii", $datac, $orac, $descrizione, $allegato, $stato, $cods, $matricola_int);
    }

    if ($stmtC->execute()) {
        echo "<p>Candidatura inserita con successo.</p>";
    } else {
        echo "<p>Errore nell'inserimento candidatura: " . $stmtC->error . "</p>";
    }

    $stmtC->close();
    $connection->close();
}
?>