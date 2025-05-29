<?php
$host = 'localhost';
$db = 'Gestione_Ospedaliera';
$user = 'postgres';
$password = 'unimi';

$conn = pg_connect("host=$host dbname=$db user=$user password=$password");

if (!$conn) {
    die("Errore di connessione al database.");
}



?>
