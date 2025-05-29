<?php
// Include il file di configurazione per la connessione al database
include '../config.php';

// Query per ottenere le disponibilità esterne
$sql_disponibilita = "SELECT de.data, de.ora, ae.indirizzo, ae.telefono, e.descrizione 
                      FROM DISPONIBILITA_ESTERNA de 
                      JOIN AMBULATORIO_ESTERNO ae ON de.ambulatorio_esterno = ae.codiceAE 
                      JOIN ESAME e ON de.esame = e.codice";

$result_disponibilita = pg_query($conn, $sql_disponibilita); // Esegue la query e memorizza il risultato
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizza Disponibilità Esterna</title>
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
    <h2>Visualizza Disponibilità Esterna</h2>
    <table>
        <tr>
            <th>Data</th>
            <th>Ora</th>
            <th>Indirizzo</th>
            <th>Telefono</th>
            <th>Esame</th>
        </tr>
        <?php
        // Verifica se ci sono risultati per le disponibilità esterne
        if (pg_num_rows($result_disponibilita) > 0) {
            // Cicla attraverso i risultati e crea una riga per ciascuna disponibilità
            while ($row = pg_fetch_assoc($result_disponibilita)) {
                echo "<tr>
                        <td>{$row['data']}</td>
                        <td>{$row['ora']}</td>
                        <td>{$row['indirizzo']}</td>
                        <td>{$row['telefono']}</td>
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
