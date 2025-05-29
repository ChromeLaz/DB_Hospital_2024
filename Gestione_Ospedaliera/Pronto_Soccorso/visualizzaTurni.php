<?php
// Include il file di configurazione per la connessione al database
include '../config.php';

// Query per ottenere i turni degli infermieri
$sql_infermieri = "SELECT data, orario_inizio, orario_fine, cf, PRONTOSOCCORSO, ospedale FROM TURNO_INFERMIERE";
$result_infermieri = pg_query($conn, $sql_infermieri); // Esegue la query e memorizza il risultato

// Query per ottenere i turni dei medici
$sql_medici = "SELECT data, orario_inizio, orario_fine, cf, PRONTOSOCCORSO, ospedale FROM TURNO_MEDICO";
$result_medici = pg_query($conn, $sql_medici); // Esegue la query e memorizza il risultato
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizza Turni</title>
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
            border-collaPRONTOSOCCORSOe: collaPRONTOSOCCORSOe;
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
        /* Stile per i pulsanti di modifica ed eliminazione */
        .modify-button, .delete-button {
            text-decoration: none;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            border: none;
        }
        .modify-button {
            background-color: #4CAF50;
        }
        .modify-button:hover {
            background-color: #45a049;
        }
        .delete-button {
            background-color: #FF6347;
        }
        .delete-button:hover {
            background-color: #FF4500;
        }
    </style>
</head>
<body>
    <h2>Visualizza Turni</h2> <!-- Titolo della pagina -->
    
    <h3>Turni Infermieri</h3> <!-- Sottotitolo per i turni degli infermieri -->
    <table>
        <tr><th>Data</th><th>Orario di Inizio</th><th>Orario di Fine</th><th>Codice Fiscale</th><th>PRONTOSOCCORSO</th><th>Ospedale</th><th>Modifica</th><th>Elimina</th></tr> <!-- Intestazione della tabella -->
        <?php
        // Verifica se ci sono risultati per i turni degli infermieri
        if (pg_num_rows($result_infermieri) > 0) {
            // Cicla attraverso i risultati e crea una riga per ciascun turno
            while ($row = pg_fetch_assoc($result_infermieri)) {
                $unique_id = base64_encode(json_encode($row)); // Crea un ID univoco per il turno
                echo "<tr>
                    <td>{$row['data']}</td>
                    <td>{$row['orario_inizio']}</td>
                    <td>{$row['orario_fine']}</td>
                    <td>{$row['cf']}</td>
                    <td>{$row['prontosoccorso']}</td>
                    <td>{$row['ospedale']}</td>
                    <td><a class='modify-button' href='modificaTurno.php?tipo=infermiere&id={$unique_id}'>Modifica Turno</a></td> <!-- Pulsante per modificare il turno -->
                    <td><a class='delete-button' href='eliminaTurno.php?tipo=infermiere&id={$unique_id}'>Elimina Turno</a></td> <!-- Pulsante per eliminare il turno -->
                </tr>";
            }
        } else {
            // Mostra un messaggio se non ci sono turni trovati
            echo '<tr><td colspan="8">Nessun turno trovato</td></tr>';
        }
        ?>
    </table>

    <h3>Turni Medici</h3> <!-- Sottotitolo per i turni dei medici -->
    <table>
        <tr><th>Data</th><th>Orario di Inizio</th><th>Orario di Fine</th><th>Codice Fiscale</th><th>PRONTOSOCCORSO</th><th>Ospedale</th><th>Modifica</th><th>Elimina</th></tr> <!-- Intestazione della tabella -->
        <?php
        // Verifica se ci sono risultati per i turni dei medici
        if (pg_num_rows($result_medici) > 0) {
            // Cicla attraverso i risultati e crea una riga per ciascun turno
            while ($row = pg_fetch_assoc($result_medici)) {
                $unique_id = base64_encode(json_encode($row)); // Crea un ID univoco per il turno
                echo "<tr>
                    <td>{$row['data']}</td>
                    <td>{$row['orario_inizio']}</td>
                    <td>{$row['orario_fine']}</td>
                    <td>{$row['cf']}</td>
                    <td>{$row['prontosoccorso']}</td>
                    <td>{$row['ospedale']}</td>
                    <td><a class='modify-button' href='modificaTurno.php?tipo=medico&id={$unique_id}'>Modifica Turno</a></td> <!-- Pulsante per modificare il turno -->
                    <td><a class='delete-button' href='eliminaTurno.php?tipo=medico&id={$unique_id}'>Elimina Turno</a></td> <!-- Pulsante per eliminare il turno -->
                </tr>";
            }
        } else {
            // Mostra un messaggio se non ci sono turni trovati
            echo '<tr><td colspan="8">Nessun turno trovato</td></tr>';
        }
        ?>
    </table>

    <div class="back-button">
        <a href="turniProntoSoccorso.php">Torna ai Turni</a> <!-- Pulsante per tornare alla pagina dei turni -->
    </div>
</body>
</html>

<?php
// Chiude la connessione al database
pg_close($conn);
?>
