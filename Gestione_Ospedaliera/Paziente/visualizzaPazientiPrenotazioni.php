<?php
include '../config.php';

// Funzione per ottenere la lista dei pazienti dal database
function getPazienti() {
    $sql = "SELECT * FROM PAZIENTE"; // Query per selezionare tutti i pazienti
    $result = pg_query($GLOBALS['conn'], $sql); // Esegui la query
    return pg_fetch_all($result); // Restituisci tutti i risultati come array associativo
}

// Funzione per ottenere la lista delle prenotazioni di un paziente specifico dal database
function getPrenotazioni($cf) {
    // Modifica la query per includere il nome dell'esame dalla tabella ESAME
    $sql = "SELECT p.*, e.descrizione AS nome_esame
            FROM PRENOTAZIONE_ESAME p
            JOIN ESAME e ON p.esame = e.codice
            WHERE p.paziente = $1"; // Query con join tra PRENOTAZIONE_ESAME ed ESAME
    $result = pg_query_params($GLOBALS['conn'], $sql, array($cf)); // Esegui la query
    return pg_fetch_all($result); // Restituisci tutti i risultati come array associativo
}

$pazienti = getPazienti(); // Ottieni la lista dei pazienti
$prenotazioni = [];
if (isset($_GET['cf'])) {
    $prenotazioni = getPrenotazioni($_GET['cf']); // Ottieni le prenotazioni del paziente selezionato
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizza Pazienti e Prenotazioni</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }
        .container {
            width: 80%;
            margin: auto;
            margin-top: 50px;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
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
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        .btn {
            padding: 10px 20px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .btn-back {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Visualizza Pazienti e Prenotazioni</h1> <!-- Titolo della pagina -->
        
        <div class="form-group">
            <label for="pazienti">Seleziona Paziente:</label> <!-- Etichetta per il menu a tendina dei pazienti -->
            <select id="pazienti" name="pazienti" onchange="location = this.value;"> <!-- Menu a tendina per selezionare un paziente -->
                <option value="">Seleziona un paziente</option> <!-- Opzione di default -->
                <?php
                // Popola il menu a tendina con la lista dei pazienti
                foreach ($pazienti as $paziente) {
                    echo "<option value='?cf={$paziente['cf']}'";
                    if (isset($_GET['cf']) && $_GET['cf'] == $paziente['cf']) {
                        echo " selected";
                    }
                    echo ">{$paziente['nome']} {$paziente['cognome']}</option>";
                }
                ?>
            </select>
        </div>

        <?php if (!empty($prenotazioni)): ?>
            <h2>Prenotazioni per <?php echo $_GET['cf']; ?></h2> <!-- Sottotitolo con il codice fiscale del paziente selezionato -->
            <table>
                <tr>
                    <th>Codice Prenotazione</th> <!-- Intestazione della colonna Codice Prenotazione -->
                    <th>Data di Prenotazione</th> <!-- Intestazione della colonna Data di Prenotazione -->
                    <th>Regime</th> <!-- Intestazione della colonna Regime -->
                    <th>Urgenza</th> <!-- Intestazione della colonna Urgenza -->
                    <th>Data</th> <!-- Intestazione della colonna Data -->
                    <th>Ora</th> <!-- Intestazione della colonna Ora -->
                    <th>Esame</th> <!-- Intestazione della colonna Esame -->
                </tr>
                <?php
                // Popola la tabella con i dati delle prenotazioni
                foreach ($prenotazioni as $prenotazione) {
                    echo "<tr>
                            <td>{$prenotazione['codice_prenotazione']}</td>
                            <td>{$prenotazione['data_di_prenotazione']}</td>
                            <td>{$prenotazione['regime']}</td>
                            <td>{$prenotazione['urgenza']}</td>
                            <td>{$prenotazione['data']}</td>
                            <td>{$prenotazione['ora']}</td>
                            <td>{$prenotazione['nome_esame']}</td>
                          </tr>";
                }
                ?>
            </table>
            
            <br>
            <br>
            <br>

        <?php elseif (isset($_GET['cf'])): ?>
            <p>Nessuna prenotazione trovata per il paziente selezionato.</p> <!-- Messaggio se non ci sono prenotazioni per il paziente selezionato -->
        <?php endif; ?>

        <a href="paziente.php" class="btn btn-back">Torna Indietro</a> <!-- Link per tornare alla pagina principale -->
    </div>
</body>
</html>
