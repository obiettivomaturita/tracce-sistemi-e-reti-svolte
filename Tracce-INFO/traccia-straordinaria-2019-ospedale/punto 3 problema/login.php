<?php
session_start();
require "db_connection.php";

$email = $_POST["email"];
$password = $_POST["password"];

$query = "SELECT IdU, Ruolo
          FROM UTENTE
          WHERE Email = ? AND PasswordU = ?";

$stmt = $connection->prepare($query);
$stmt->bind_param("ss", $email, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Credenziali non valide.");
}

$row = $result->fetch_assoc();
$_SESSION["IdU"] = $row["IdU"];
$_SESSION["Ruolo"] = $row["Ruolo"];

echo "<!DOCTYPE html>";
echo "<html lang='it'>";
echo "<head><meta charset='UTF-8'><title>Menu</title></head>";
echo "<body>";
echo "<h2>Menu</h2>";

if ($_SESSION["Ruolo"] == "FARMACIA") {
    echo "<p>Accesso come FARMACIA</p>";
    echo "<a href='farmacia_form.html'>Elenchi giornalieri farmaci</a>";
} else if ($_SESSION["Ruolo"] == "MEDICO") {
    echo "<p>Accesso come MEDICO</p>";
    echo "<a href='storico_form.html'>Storico dati biometrici paziente</a>";
} else {
    echo "<p>Ruolo non riconosciuto.</p>";
}

echo "</body></html>";

$stmt->close();
$connection->close();
?>

