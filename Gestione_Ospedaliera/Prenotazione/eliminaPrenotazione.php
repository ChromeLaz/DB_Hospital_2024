<?php
// Includere il file di configurazione del database
include '../config.php';

// Funzione per ottenere tutte le prenotazioni
function getPrenotazioni() {
    // Definire la query SQL per selezionare tutte le prenotazioni
    $sql = "SELECT * FROM PRENOTAZIONE_ESAME";
    // Eseguire la query e restituire i risultati
    $result = pg_query($GLOBALS['conn'], $sql);
    return pg_fetch_all($result);
}

// Funzione per rimuovere una prenotazione in base al codice
function removePrenotazione($codice_prenotazione) {
    // Definire la query SQL per eliminare una prenotazione
    $sql = "DELETE FROM PRENOTAZIONE_ESAME WHERE codice_prenotazione = '$codice_prenotazione'";
    // Eseguire la query e restituire il risultato
    return pg_query($GLOBALS['conn'], $sql);
}

// Ottenere tutte le prenotazioni
$prenotazioni = getPrenotazioni();
$success = null;

// Controllare se la richiesta è POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ottenere il codice della prenotazione dal modulo
    $codice_prenotazione = $_POST['codice_prenotazione'];
    // Rimuovere la prenotazione e memorizzare il risultato
    $success = removePrenotazione($codice_prenotazione);
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elimina Prenotazione</title>
    <style>
        /* Stile per il corpo della pagina */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }
        /* Stile per il contenitore principale */
        .container {
            width: 80%;
            margin: auto;
            margin-top: 50px;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        /* Stile per i gruppi di form */
        .form-group {
            margin-bottom: 15px;
        }
        /* Stile per le etichette */
        label {
            display: block;
            margin-bottom: 5px;
        }
        /* Stile per gli input di testo e select */
        input[type="text"], select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        /* Stile per i pulsanti */
        .btn {
            padding: 10px 20px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        /* Stile per i pulsanti al passaggio del mouse */
        .btn:hover {
            background-color: #0056b3;
        }
        /* Stile per i messaggi di successo ed errore */
        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Elimina Prenotazione</h1>
        <!-- Mostrare un messaggio di successo o errore se presente -->
        <?php if ($success !== null): ?>
            <div class="message <?php echo $success ? 'success' : 'error'; ?>">
                <?php echo $success ? 'Prenotazione eliminata con successo!' : 'Errore nell\'eliminazione della prenotazione.'; ?>
            </div>
        <?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label for="codice_prenotazione">Seleziona Prenotazione:</label>
                <!-- Menu a tendina per selezionare la prenotazione da eliminare -->
                <select id="codice_prenotazione" name="codice_prenotazione" required>
                    <?php foreach ($prenotazioni as $prenotazione): ?>
                        <option value="<?php echo $prenotazione['codice_prenotazione']; ?>">
                            Codice: <?php echo $prenotazione['codice_prenotazione']; ?>, Paziente: <?php echo $prenotazione['paziente']; ?>, Data: <?php echo $prenotazione['data']; ?>, Ora: <?php echo $prenotazione['ora']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn">Elimina Prenotazione</button>
            <br>
            <br>

        </form>
        <a href="prenotazione.php" class="btn" style="margin-top: 20px;">Torna Indietro</a>
    </div>
</body>
</html>
