<?php
// Include il file di configurazione per la connessione al database
include '../config.php';

// Query per ottenere gli ospedali disponibili
$sql_ospedali = "SELECT codice, nome FROM OSPEDALE"; // Usa 'codice' come il nome corretto della colonna
$result_ospedali = pg_query($conn, $sql_ospedali); // Esegue la query e memorizza il risultato

// Inizializza la variabile per memorizzare l'ospedale selezionato
$ospedale_selezionato = null;

// Verifica se un ospedale è stato selezionato
if (isset($_GET['ospedale'])) {
    $ospedale_selezionato = $_GET['ospedale'];

    // Query per ottenere gli ambulatori interni nell'ospedale selezionato
    $sql_ambulatori = "SELECT ai.codiceAI, s.numero, s.piano FROM AMBULATORIO_INTERNO ai 
                       JOIN STANZA s ON ai.stanza = s.id 
                       WHERE s.reparto_ospedale = $ospedale_selezionato";
    $result_ambulatori = pg_query($conn, $sql_ambulatori); // Esegue la query e memorizza il risultato
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizza Ambulatori Interni</title>
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
        /* Stile per il form di selezione dell'ospedale */
        form {
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: left;
        }
        form select, form input[type="submit"] {
            width: 100%;
            padding: 8px;
            margin: 8px 0;
            box-sizing: border-box;
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
    <h2>Visualizza Ambulatori Interni</h2>
    <form method="get" action="visualizzaAmbulatoriInterni.php">
        <label for="ospedale">Seleziona Ospedale:</label>
        <select name="ospedale" id="ospedale" required>
            <?php
            // Popola il select degli ospedali disponibili
            while ($row = pg_fetch_assoc($result_ospedali)) {
                $selected = ($row['codice'] == $ospedale_selezionato) ? 'selected' : '';
                echo "<option value='{$row['codice']}' $selected>{$row['nome']}</option>";
            }
            ?>
        </select>
        <input type="submit" value="Visualizza Ambulatori">
    </form>

    <?php if ($ospedale_selezionato): ?>
    <table>
        <tr><th>ID</th><th>Numero Stanza</th><th>Piano</th><th>Modifica</th><th>Elimina</th></tr>
        <?php
        // Verifica se ci sono risultati per gli ambulatori interni
        if (pg_num_rows($result_ambulatori) > 0) {
            // Cicla attraverso i risultati e crea una riga per ciascun ambulatorio
            while ($row = pg_fetch_assoc($result_ambulatori)) {
                echo "<tr>
                    <td>{$row['codiceai']}</td>
                    <td>{$row['numero']}</td>
                    <td>{$row['piano']}</td>
                    <td><a class='modify-button' href='modificaAmbulatorioInterno.php?id={$row['codiceai']}'>Modifica</a></td> <!-- Pulsante per modificare l'ambulatorio -->
                    <td><a class='delete-button' href='eliminaAmbulatorioInterno.php?id={$row['codiceai']}'>Elimina</a></td> <!-- Pulsante per eliminare l'ambulatorio -->
                </tr>";
            }
        } else {
            // Mostra un messaggio se non ci sono ambulatori trovati
            echo '<tr><td colspan="5">Nessun ambulatorio trovato</td></tr>';
        }
        ?>
    </table>
    <?php endif; ?>

    <div class="back-button" style="margin-top: 20px;">
        <a href="aggiungiAmbulatorioInterno.php">Aggiungi Ambulatorio Interno</a> <!-- Pulsante per aggiungere un nuovo ambulatorio -->
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
