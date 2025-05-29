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

// Funzione per ottenere una singola prenotazione in base al codice
function getPrenotazione($codice_prenotazione) {
    // Definire la query SQL per selezionare una prenotazione specifica
    $sql = "SELECT * FROM PRENOTAZIONE_ESAME WHERE codice_prenotazione = '$codice_prenotazione'";
    // Eseguire la query e restituire il risultato
    $result = pg_query($GLOBALS['conn'], $sql);
    return pg_fetch_assoc($result);
}

// Funzione per ottenere tutti i pazienti
function getPazienti() {
    // Definire la query SQL per selezionare tutti i pazienti
    $sql = "SELECT cf, nome, cognome FROM PAZIENTE";
    // Eseguire la query e restituire i risultati
    $result = pg_query($GLOBALS['conn'], $sql);
    return pg_fetch_all($result);
}

// Funzione per ottenere tutti gli esami
function getEsami() {
    // Definire la query SQL per selezionare tutti gli esami
    $sql = "SELECT * FROM ESAME";
    // Eseguire la query e restituire i risultati
    $result = pg_query($GLOBALS['conn'], $sql);
    return pg_fetch_all($result);
}

// Funzione per aggiornare una prenotazione
function updatePrenotazione($codice_prenotazione, $data_di_prenotazione, $regime, $urgenza, $paziente, $medico_prescrittore, $avvertenze) {
    // Definire la query SQL per aggiornare una prenotazione
    $sql = "UPDATE PRENOTAZIONE_ESAME 
            SET data_di_prenotazione = '$data_di_prenotazione', regime = '$regime', urgenza = '$urgenza', paziente = '$paziente', 
                medico_prescrittore = " . ($medico_prescrittore ? "'$medico_prescrittore'" : "NULL") . ", avvertenze = " . ($avvertenze ? "'$avvertenze'" : "NULL") . "
            WHERE codice_prenotazione = '$codice_prenotazione'";
    // Eseguire la query e restituire il risultato
    return pg_query($GLOBALS['conn'], $sql);
}

// Ottenere tutte le prenotazioni e i pazienti
$prenotazioni = getPrenotazioni();
$pazienti = getPazienti();
$prenotazione = null;
$esami = getEsami();
$success = null;

// Controllare se la richiesta è POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Se viene inviato il codice prenotazione senza richiesta di aggiornamento
    if (isset($_POST['codice_prenotazione']) && !isset($_POST['update'])) {
        // Ottenere la prenotazione specifica
        $prenotazione = getPrenotazione($_POST['codice_prenotazione']);
    } elseif (isset($_POST['update'])) {
        // Se viene richiesta l'aggiornamento della prenotazione
        $codice_prenotazione = $_POST['codice_prenotazione'];
        $data_di_prenotazione = $_POST['data_di_prenotazione'];
        $regime = $_POST['regime'];
        $urgenza = $_POST['urgenza'];
        $paziente = $_POST['paziente'];
        $medico_prescrittore = $_POST['medico_prescrittore'];
        $avvertenze = $_POST['avvertenze'];
        // Aggiornare la prenotazione e memorizzare il risultato
        $success = updatePrenotazione($codice_prenotazione, $data_di_prenotazione, $regime, $urgenza, $paziente, $medico_prescrittore, $avvertenze);
        // Ottenere di nuovo la prenotazione aggiornata
        $prenotazione = getPrenotazione($codice_prenotazione);
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Prenotazione</title>
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
        /* Stile per gli input di testo, date e select */
        input[type="text"], input[type="date"], select {
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
    <!-- Inizio del contenitore principale della pagina -->
    <div class="container">
        <!-- Intestazione della pagina -->
        <h1>Modifica Prenotazione</h1>
        
        <!-- Mostrare un messaggio di successo o errore se presente -->
        <?php if ($success !== null): ?>
            <div class="message <?php echo $success ? 'success' : 'error'; ?>">
                <?php echo $success ? 'Prenotazione modificata con successo!' : 'Errore nella modifica della prenotazione.'; ?>
            </div>
        <?php endif; ?>
        
        <!-- Form per selezionare la prenotazione da modificare -->
        <?php if ($prenotazione === null): ?>
            <form method="post">
                <div class="form-group">
                    <label for="codice_prenotazione">Seleziona Prenotazione:</label>
                    <select id="codice_prenotazione" name="codice_prenotazione" required>
                        <?php foreach ($prenotazioni as $pren): ?>
                            <option value="<?php echo $pren['codice_prenotazione']; ?>">
                                Codice: <?php echo $pren['codice_prenotazione']; ?>, Paziente: <?php echo $pren['paziente']; ?>, Data: <?php echo $pren['data']; ?>, Ora: <?php echo $pren['ora']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn">Modifica Prenotazione</button>
            </form>
        
        <!-- Form per modificare la prenotazione selezionata -->
        <?php else: ?>
            <form method="post">
                <!-- Campi hidden per mantenere lo stato del modulo -->
                <input type="hidden" name="update" value="1">
                <input type="hidden" name="codice_prenotazione" value="<?php echo $prenotazione['codice_prenotazione']; ?>">
                
                <!-- Campo per selezionare il codice fiscale del paziente -->
                <div class="form-group">
                    <label for="cf">Codice Fiscale Paziente:</label>
                    <select id="cf" name="paziente" required>
                        <?php foreach ($pazienti as $paziente): ?>
                            <option value="<?php echo $paziente['cf']; ?>" <?php echo $prenotazione['paziente'] == $paziente['cf'] ? 'selected' : ''; ?>>
                                <?php echo $paziente['cf'] . ' - ' . $paziente['nome'] . ' ' . $paziente['cognome']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Campo per modificare la data di prenotazione -->
                <div class="form-group">
                    <label for="data_di_prenotazione">Data di Prenotazione:</label>
                    <input type="date" id="data_di_prenotazione" name="data_di_prenotazione" value="<?php echo $prenotazione['data_di_prenotazione']; ?>" required>
                </div>
                
                <!-- Campo per selezionare il regime -->
                <div class="form-group">
                    <label for="regime">Regime:</label>
                    <select id="regime" name="regime" required>
                        <option value="Pubblico" <?php echo $prenotazione['regime'] == 'Pubblico' ? 'selected' : ''; ?>>Pubblico</option>
                        <option value="Privato" <?php echo $prenotazione['regime'] == 'Privato' ? 'selected' : ''; ?>>Privato</option>
                    </select>
                </div>
                
                <!-- Campo per selezionare l'urgenza -->
                <div class="form-group">
                    <label for="urgenza">Urgenza:</label>
                    <select id="urgenza" name="urgenza" required>
                        <option value="Verde" <?php echo $prenotazione['urgenza'] == 'Verde' ? 'selected' : ''; ?>>Verde</option>
                        <option value="Giallo" <?php echo $prenotazione['urgenza'] == 'Giallo' ? 'selected' : ''; ?>>Giallo</option>
                        <option value="Rosso" <?php echo $prenotazione['urgenza'] == 'Rosso' ? 'selected' : ''; ?>>Rosso</option>
                    </select>
                </div>
                
                <!-- Campo per modificare il medico prescrittore -->
                <div class="form-group">
                    <label for="medico">Medico Prescrittore:</label>
                    <input type="text" id="medico_prescrittore" name="medico_prescrittore" value="<?php echo $prenotazione['medico_prescrittore']; ?>">
                </div>
                
                <!-- Campo per modificare le avvertenze -->
                <div class="form-group">
                    <label for="avvertenze">Avvertenze:</label>
                    <textarea id="avvertenze" name="avvertenze" rows="4"><?php echo $prenotazione['avvertenze']; ?></textarea>
                </div>
                
                <!-- Bottone per aggiornare la prenotazione -->
                <button type="submit" class="btn">Aggiorna Prenotazione</button>
            </form>
        <?php endif; ?>
        <br>
        <br>
        
        <!-- Link per tornare alla pagina precedente -->
        <a href="prenotazione.php" class="btn" style="margin-top: 20px;">Torna Indietro</a>
    </div>
</body>
</html>
