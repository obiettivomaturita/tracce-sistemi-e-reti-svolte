<?php

$connection=new mysqli("localhost","root","nuova_password","Ospedale2016");
if($connection->connect_error){
    die("Errore di connessione". $connection->connect_error);
}
?>

