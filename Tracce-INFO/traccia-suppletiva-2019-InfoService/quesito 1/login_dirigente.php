<?php
require "db_connection.php";

$email = $_POST["email"];
$password = $_POST["password"];

$query = "SELECT IdT
          FROM TECNICO
          WHERE Email = ?
            AND PasswordT = ?
            AND Ruolo = 'DIRIGENTE'";

$stmt = $connection->prepare($query);
$stmt->bind_param("ss", $email, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Accesso negato: credenziali non valide o ruolo non autorizzato.");
}

header("Location: menu_dirigente.html");
exit;
?>




