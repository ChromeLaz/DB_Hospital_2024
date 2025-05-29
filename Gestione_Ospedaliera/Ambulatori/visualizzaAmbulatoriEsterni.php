<?php
// Include il file di configurazione per la connessione al database
include '../config.php';

// Query per ottenere gli ambulatori esterni
$sql_ambulatori = "SELECT codiceae, indirizzo, telefono, orario_apertura FROM ambulatorio_esterno";
$result_ambulatori = pg_query($conn, $sql_ambulatori); // Esegue la query e memorizza il risultato
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizza Ambulatori Esterni</title>
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
    <h2>Visualizza Ambulatori Esterni</h2>
    <table>
        <tr>
            <th>Codice</th>
            <th>Indirizzo</th>
            <th>Telefono</th>
            <th>Orario di Apertura</th>
            <th>Modifica</th>
            <th>Elimina</th>
        </tr>
        <?php
        // Verifica se ci sono risultati per gli ambulatori esterni
        if (pg_num_rows($result_ambulatori) > 0) {
            // Cicla attraverso i risultati e crea una riga per ciascun ambulatorio
            while ($row = pg_fetch_assoc($result_ambulatori)) {
                echo "<tr>
                        <td>{$row['codiceae']}</td>
                        <td>{$row['indirizzo']}</td>
                        <td>{$row['telefono']}</td>
                        <td>{$row['orario_apertura']}</td>
                        <td><a class='modify-button' href='modificaAmbulatorioEsterno.php?codiceae={$row['codiceae']}'>Modifica</a></td>
                        <td><a class='delete-button' href='eliminaAmbulatorioEsterno.php?codiceae={$row['codiceae']}'>Elimina</a></td>
                    </tr>";
            }
        } else {
            // Mostra un messaggio se non ci sono ambulatori trovati
            echo '<tr><td colspan="6">Nessun ambulatorio esterno trovato</td></tr>';
        }
        ?>
    </table>

    <div class="back-button" style="margin-top: 20px;">
        <a href="aggiungiAmbulatorioEsterno.php">Aggiungi Ambulatorio Esterno</a> <!-- Pulsante per aggiungere un nuovo ambulatorio -->
    </div>
    <br>
    <br>
    <div class="back-button">
        <a href="ambulatorio.php">Torna indietro</a>
    </div>
</body>
</html>

<?php
// Chiude la connessione al database
pg_close($conn);
?>




