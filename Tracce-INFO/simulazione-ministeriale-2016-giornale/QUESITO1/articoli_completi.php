<?php
require "db_connection.php";
session_start();

if (!isset($_SESSION["IdU"])) {
    die("Accesso non autorizzato.");
}

$ruolo = $_SESSION["Ruolo"];
if ($ruolo != "Abbonato" && $ruolo != "Redattore" && $ruolo != "Direttore" && $ruolo != "Giornalista") {
    die("Permesso negato.");
}

$query = "
SELECT A.IdA, A.Titolo, A.DataP, U.Nome, U.Cognome
FROM ARTICOLO A
INNER JOIN UTENTE U ON U.IdU = A.IdU
ORDER BY A.DataP DESC
";

$stmt = $connection->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Articoli completi</title>
</head>
<body>
  <h1>Articoli</h1>

  <?php
  if ($result->num_rows > 0) {
      echo "<table border='1'>
              <thead>
                <tr>
                  <th>ID</th><th>Titolo</th><th>Data</th><th>Autore</th>
                </tr>
              </thead>
              <tbody>";
      while ($row = $result->fetch_assoc()) {
          echo "<tr>
                  <td>{$row['IdA']}</td>
                  <td>{$row['Titolo']}</td>
                  <td>{$row['DataP']}</td>
                  <td>{$row['Nome']} {$row['Cognome']}</td>
                </tr>";
      }
      echo "</tbody></table>";
  } else {
      echo "<p>Nessun articolo presente.</p>";
  }

  $stmt->close();
  $connection->close();
  ?>
</body>
</html>