<?php
// Include il file di configurazione per la connessione al database
include '../config.php';

// Query per ottenere le disponibilità interne
$sql_disponibilita = "SELECT di.data, di.ora, ai.stanza, ai.codiceAI AS ambulatorio_interno, e.descrizione, s.numero AS numero_stanza, o.nome AS nome_ospedale
                      FROM DISPONIBILITA_INTERNA di 
                      JOIN AMBULATORIO_INTERNO ai ON di.ambulatorio_interno = ai.codiceAI 
                      JOIN STANZA s ON ai.stanza = s.id 
                      JOIN OSPEDALE o ON s.reparto_ospedale = o.codice
                      JOIN ESAME e ON di.esame = e.codice";

$result_disponibilita = pg_query($conn, $sql_disponibilita); // Esegue la query e memorizza il risultato
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizza Disponibilità Interna</title>
    <style>
        /* Stile per il corpo della pagina */
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f0f0f0;
        }
        /* Stile per il pulsante di ritorno */
        .back-button a {
            text-decoration: none;
            color: #333;
            padding: 10px 20px;
            background-color: #ddd;
            border-radius: 4px;
        }
        .back-button a:hover {
            background-color: #ccc;
        }
        /* Stile per la tabella */
        table {
            margin: 20px auto;
            border-collapse: collapse;
            width: 80%;
            background-color: #fff;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Visualizza Disponibilità Interna</h2>
    <table>
        <tr>
            <th>Data</th>
            <th>Ora</th>
            <th>Numero Stanza</th>
            <th>Ospedale</th>
            <th>Esame</th>
        </tr>
        <?php
        // Verifica se ci sono risultati per le disponibilità interne
        if (pg_num_rows($result_disponibilita) > 0) {
            // Cicla attraverso i risultati e crea una riga per ciascuna disponibilità
            while ($row = pg_fetch_assoc($result_disponibilita)) {
                echo "<tr>
                        <td>{$row['data']}</td>
                        <td>{$row['ora']}</td>
                        <td>{$row['numero_stanza']}</td>
                        <td>{$row['nome_ospedale']}</td>
                        <td>{$row['descrizione']}</td>
                    </tr>";

            }
        } else {
            // Mostra un messaggio se non ci sono disponibilità trovate
            echo '<tr><td colspan="5">Nessuna disponibilità trovata</td></tr>';
        }
        ?>
    </table>

    <div class="back-button">
        <a href="javascript:history.back()">Torna Indietro</a>
    </div>
</body>
</html>

<?php
// Chiude la connessione al database
pg_close($conn);
?>
