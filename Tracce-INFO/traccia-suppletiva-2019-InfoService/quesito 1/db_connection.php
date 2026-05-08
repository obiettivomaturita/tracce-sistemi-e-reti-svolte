<?php

$connection=new mysqli("localhost","root","nuova_password","InfoService_2019");
if($connection->connect_error){
    die("Errore di connessione". $connection->connect_error);
}
?>

