<?php
session_start();
require "db_connection.php";

$email = $_POST["email"];
$password = $_POST["password"];

$query = "SELECT IdFr, Nome, Cognome
          FROM FARMACISTA
          WHERE Email = ? AND PasswordF = ?";

$stmt = $connection->prepare($query);
$stmt->bind_param("ss", $email, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    $_SESSION["IdU"] = $row["IdFr"];
    $_SESSION["Ruolo"] = "FARMACIA";
    $_SESSION["Nome"] = $row["Nome"];
    $_SESSION["Cognome"] = $row["Cognome"];

    echo "<!DOCTYPE html>";
    echo "<html lang='it'>";
    echo "<head><meta charset='UTF-8'><title>Menu</title></head>";
    echo "<body>";

    echo "<h2>Menu</h2>";
    echo "<p>Accesso come FARMACIA</p>";
    echo "<p>Benvenuto ".$_SESSION["Nome"]." ".$_SESSION["Cognome"]."</p>";

    echo "<a href='farmacia_form.html'>Elenchi giornalieri farmaci</a>";
    echo "<br><br>";
    echo "<a href='logout.php'>Logout</a>";

    echo "</body>";
    echo "</html>";

    $stmt->close();
    $connection->close();
    exit;
}

$stmt->close();

$query = "SELECT IdM, Nome, Cognome
          FROM MEDICO
          WHERE Email = ? AND PasswordM = ?";

$stmt = $connection->prepare($query);
$stmt->bind_param("ss", $email, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    $_SESSION["IdU"] = $row["IdM"];
    $_SESSION["Ruolo"] = "MEDICO";
    $_SESSION["Nome"] = $row["Nome"];
    $_SESSION["Cognome"] = $row["Cognome"];

    echo "<!DOCTYPE html>";
    echo "<html lang='it'>";
    echo "<head><meta charset='UTF-8'><title>Menu</title></head>";
    echo "<body>";

    echo "<h2>Menu</h2>";
    echo "<p>Accesso come MEDICO</p>";
    echo "<p>Benvenuto ".$_SESSION["Nome"]." ".$_SESSION["Cognome"]."</p>";

    echo "<p>Area medico.</p>";
    echo "<br>";
    echo "<a href='logout.php'>Logout</a>";

    echo "</body>";
    echo "</html>";
} else {
    echo "<!DOCTYPE html>";
    echo "<html lang='it'>";
    echo "<head><meta charset='UTF-8'><title>Errore login</title></head>";
    echo "<body>";

    echo "<p>Credenziali non valide.</p>";
    echo "<a href='login_form.html'>Torna al login</a>";

    echo "</body>";
    echo "</html>";
}

$stmt->close();
$connection->close();
?>

