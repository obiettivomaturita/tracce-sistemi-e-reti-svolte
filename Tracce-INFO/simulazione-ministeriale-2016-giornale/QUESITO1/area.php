<?php
session_start();

if (!isset($_SESSION["IdU"])) {
    header("Location: login.html");
    exit;
}

$ruolo = $_SESSION["Ruolo"];
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Area riservata</title>
</head>
<body>
  <h1>Area riservata</h1>

  <?php
  echo "<p>Ruolo: $ruolo</p>";
  echo "<p><a href='logout.php'>Logout</a></p>";

  if ($ruolo == "Abbonato") {
      echo "<p>Accesso agli articoli completi.</p>";
      echo "<p><a href='articoli_completi.php'>Vai agli articoli</a></p>";
  } else if ($ruolo == "Redattore" || $ruolo == "Direttore" || $ruolo == "Giornalista") {
      echo "<p>Gestione contenuti.</p>";
      echo "<p><a href='form_inserisci_articolo.html'>Inserisci articolo</a></p>";
      echo "<p><a href='elenco_articoli.php'>Elenco articoli</a></p>";
  } else {
      echo "<p>Ruolo non gestito.</p>";
  }
  ?>
</body>
</html>