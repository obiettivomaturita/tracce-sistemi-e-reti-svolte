<?php

$connection=new mysqli("localhost","root","nuova_password","Taxi2016");
if($connection->connect_error){
    die("Errore di connessione". $connection->connect_error);
}
?>

