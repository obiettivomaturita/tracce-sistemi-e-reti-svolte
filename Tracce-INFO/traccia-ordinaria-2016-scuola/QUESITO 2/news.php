<?php
require "db_connection.php";

$idNews = $_POST["IdNews"];

$query = "
    SELECT N.IdNews, N.Titolo, N.ContenutoMultimediale, N.DataIns,
           U.Nome, U.Cognome, U.Email
    FROM NEWS N
    INNER JOIN UTENTE U ON U.IdUtente = N.IdUtente
    WHERE N.IdNews = ?
";

$stmt = $connection->prepare($query);
$stmt->bind_param("i", $idNews);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Dettaglio News</title>
</head>
<body>
    <h1>Articolo</h1>

    <?php
    if ($result->num_rows > 0) {

        echo "<table border='1'>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titolo</th>
                        <th>Autore</th>
                        <th>Email autore</th>
                        <th>Data inserimento</th>
                        <th>Contenuto multimediale</th>
                    </tr>
                </thead>
                <tbody>";

        while ($row = $result->fetch_assoc()) {

            $autore = $row["Nome"] . " " . $row["Cognome"];

            echo "<tr>
                    <td>{$row['IdNews']}</td>
                    <td>{$row['Titolo']}</td>
                    <td>$autore</td>
                    <td>{$row['Email']}</td>
                    <td>{$row['DataIns']}</td>
                    <td>{$row['ContenutoMultimediale']}</td>
                  </tr>";

            echo "<tr>
                    <th>Contenuto</th>
                    <td colspan='5'>" . nl2br($row["Contenuto"]) . "</td>
                  </tr>";
        }

        echo "</tbody></table>";

    } else {
        echo "<p>Nessuna news trovata con l'ID indicato.</p>";
    }

    $stmt->close();
    $connection->close();
    ?>
</body>
</html>