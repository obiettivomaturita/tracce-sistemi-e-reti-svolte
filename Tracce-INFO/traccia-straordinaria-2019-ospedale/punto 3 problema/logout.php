<?php
session_start();
session_destroy();

echo "<!DOCTYPE html>";
echo "<html lang='it'>";
echo "<head><meta charset='UTF-8'><title>Logout</title></head>";
echo "<body>";

echo "<p>Logout effettuato correttamente.</p>";
echo "<a href='login_form.html'>Torna al login</a>";

echo "</body>";
echo "</html>";
?>
