<?php
require "db_connection.php";

$matricola = $_POST["Matricola"];
$cf = $_POST["CF"];
$dataInizio = $_POST["DataInizio"];
$dataFine = $_POST["DataFine"];

if (empty($matricola) || empty($cf) || empty($dataInizio) || empty($dataFine)) {
    die("Tutti i campi sono obbligatori.");
}

if (!is_numeric($matricola)) {
    die("La Matricola del medico deve essere numerica.");
}

$query = "
SELECT 
  P.CF, P.Nome, P.Cognome,
  M.DataMis, M.PressioneMin, M.PressioneMax,
  M.FrequenzaCardiaca, M.Temperatura
FROM MISURAZIONE M
INNER JOIN PAZIENTE P ON P.CF = M.CF
WHERE M.CF = ?
  AND M.DataMis BETWEEN ? AND ?
  AND EXISTS (
      SELECT 1
      FROM VISITA V
      WHERE V.Matricola = ?
        AND V.CF = P.CF
  )
ORDER BY M.DataMis
";

$stmt = $connection->prepare($query);
if (!$stmt) {
    die("Errore nella preparazione della query: " . $connection->error);
}

$stmt->bind_param("sssi", $cf, $dataInizio, $dataFine, $matricola);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Risultato misurazioni</title>
</head>
<body>
  <h1>Misurazioni nel periodo richiesto</h1>

  <?php
  if ($result->num_rows > 0) {

      $prima = $result->fetch_assoc();
      echo "<p><b>Paziente:</b> {$prima['Nome']} {$prima['Cognome']} (CF: {$prima['CF']})</p>";

      echo "<table border='1'>
              <thead>
                <tr>
                  <th>Data e ora</th>
                  <th>Pressione Min</th>
                  <th>Pressione Max</th>
                  <th>Frequenza Cardiaca</th>
                  <th>Temperatura</th>
                </tr>
              </thead>
              <tbody>";

      echo "<tr>
              <td>{$prima['DataMis']}</td>
              <td>{$prima['PressioneMin']}</td>
              <td>{$prima['PressioneMax']}</td>
              <td>{$prima['FrequenzaCardiaca']}</td>
              <td>{$prima['Temperatura']}</td>
            </tr>";

      while ($row = $result->fetch_assoc()) {
          echo "<tr>
                  <td>{$row['DataMis']}</td>
                  <td>{$row['PressioneMin']}</td>
                  <td>{$row['PressioneMax']}</td>
                  <td>{$row['FrequenzaCardiaca']}</td>
                  <td>{$row['Temperatura']}</td>
                </tr>";
      }

      echo "</tbody></table>";

  } else {
      echo "<p>Nessuna misurazione trovata nel periodo oppure paziente non associato al medico.</p>";
  }

  $stmt->close();
  $connection->close();
  ?>
</body>
</html>