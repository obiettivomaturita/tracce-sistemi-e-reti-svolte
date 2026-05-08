<?php
require "db_connection.php";
session_start();

$email = $_POST["Email"];
$password = $_POST["PasswordU"];

if (empty($email) || empty($password)) {
    die("Tutti i campi sono obbligatori.");
}

$query = "
SELECT IdU, PasswordU, Ruolo
FROM UTENTE
WHERE Email = ?
";

$stmt = $connection->prepare($query);
if (!$stmt) {
    die("Errore nella preparazione della query: " . $connection->error);
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Credenziali non valide.");
}

$row = $result->fetch_assoc();

if (!password_verify($password, $row["PasswordU"])) {
    die("Credenziali non valide.");
}

$_SESSION["IdU"] = $row["IdU"];
$_SESSION["Ruolo"] = $row["Ruolo"];

$stmt->close();
$connection->close();

header("Location: area_riservata.php");
exit;
?>