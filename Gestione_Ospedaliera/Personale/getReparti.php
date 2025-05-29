<?php
// Includere il file di configurazione del database
include '../config.php';

// Ottenere l'ID dell'ospedale dalla richiesta GET e convertirlo in un intero
$ospedale = intval($_GET['ospedale']);

// Preparare la query per selezionare i reparti basati sull'ospedale
$query = "SELECT nome FROM REPARTO WHERE ospedale = $1";

// Preparare la query con pg_prepare
$result = pg_prepare($conn, "reparti_query", $query);

// Eseguire la query con pg_execute passando l'ID dell'ospedale come parametro
$result = pg_execute($conn, "reparti_query", array($ospedale));

// Inizializzare un array per memorizzare i reparti
$reparti = array();

// Iterare sui risultati della query e aggiungere ogni reparto all'array
while ($row = pg_fetch_assoc($result)) {
    $reparti[] = $row;
}

// Impostare l'intestazione del contenuto come JSON
header('Content-Type: application/json');

// Codificare l'array dei reparti in formato JSON e inviarlo come risposta
echo json_encode($reparti);
?>
