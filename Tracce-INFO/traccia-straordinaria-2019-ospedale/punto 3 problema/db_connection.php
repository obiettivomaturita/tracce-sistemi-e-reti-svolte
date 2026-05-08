<?php

$connection=new mysqli("localhost","root","nuova_password","Ospedale_2019");
if($connection->connect_error){
    die("Errore di connessione". $connection->connect_error);
}
?>

