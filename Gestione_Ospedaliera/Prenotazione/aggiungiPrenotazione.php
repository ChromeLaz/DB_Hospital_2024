<?php
// Includere il file di configurazione del database
include '../config.php';

// Funzione per ottenere i pazienti
function getPazienti() {
    $sql = "SELECT cf, nome, cognome FROM PAZIENTE";
    $result = pg_query($GLOBALS['conn'], $sql);
    return pg_fetch_all($result);
}

// Funzione per ottenere gli esami
function getEsami() {
    $sql = "SELECT * FROM ESAME";
    $result = pg_query($GLOBALS['conn'], $sql);
    return pg_fetch_all($result);
}

// Funzione per ottenere le disponibilità interne di un esame
function getAmbulatoriInterni($esame) {
    $sql = "SELECT * FROM DISPONIBILITA_INTERNA WHERE esame = '$esame'";
    $result = pg_query($GLOBALS['conn'], $sql);
    return pg_fetch_all($result);
}

// Funzione per ottenere le disponibilità esterne di un esame
function getAmbulatoriEsterni($esame) {
    $sql = "SELECT * FROM DISPONIBILITA_ESTERNA WHERE esame = '$esame'";
    $result = pg_query($GLOBALS['conn'], $sql);
    return pg_fetch_all($result);
}

// Funzione per aggiungere una prenotazione
function addPrenotazione($data_di_prenotazione, $regime, $urgenza, $paziente, $esame, $ambulatorio, $tipo_ambulatorio, $data, $ora, $medico_prescrittore, $avvertenze) {
    $sqlPrenotazione = "INSERT INTO PRENOTAZIONE_ESAME (data_di_prenotazione, regime, urgenza, data, ora, paziente, esame, medico_prescrittore, avvertenze) 
                        VALUES ('$data_di_prenotazione', '$regime', '$urgenza', '$data', '$ora', '$paziente', '$esame', " . ($medico_prescrittore ? "'$medico_prescrittore'" : "NULL") . ", " . ($avvertenze ? "'$avvertenze'" : "NULL") . ")";
    $result = pg_query($GLOBALS['conn'], $sqlPrenotazione);
    if ($result) {
        $codice_prenotazione = pg_fetch_result(pg_query($GLOBALS['conn'], "SELECT lastval()"), 0, 0);
        if ($tipo_ambulatorio == 'interno') {
            $sqlPrenotaInterno = "INSERT INTO PRENOTA_INTERNO (codice_prenotazione, codice_ambulatorio_interno)
                                  VALUES ('$codice_prenotazione', '$ambulatorio')";
            pg_query($GLOBALS['conn'], $sqlPrenotaInterno);
            removeDisponibilitaInterna($ambulatorio, $data, $ora, $esame);
        } else {
            $sqlPrenotaEsterno = "INSERT INTO PRENOTA_ESTERNO (codice_prenotazione, codice_ambulatorio_esterno)
                                  VALUES ('$codice_prenotazione', '$ambulatorio')";
            pg_query($GLOBALS['conn'], $sqlPrenotaEsterno);
            removeDisponibilitaEsterna($ambulatorio, $data, $ora, $esame);
        }
        return true;
    } else {
        error_log("Errore nell'inserimento di PRENOTAZIONE_ESAME: " . pg_last_error($GLOBALS['conn']));
        return false;
    }
}

// Funzione per rimuovere la disponibilità interna
function removeDisponibilitaInterna($ambulatorio, $data, $ora, $esame) {
    $sql = "DELETE FROM DISPONIBILITA_INTERNA WHERE ambulatorio_interno = '$ambulatorio' AND data = '$data' AND ora = '$ora' AND esame = '$esame'";
    return pg_query($GLOBALS['conn'], $sql);
}

// Funzione per rimuovere la disponibilità esterna
function removeDisponibilitaEsterna($ambulatorio, $data, $ora, $esame) {
    $sql = "DELETE FROM DISPONIBILITA_ESTERNA WHERE ambulatorio_esterno = '$ambulatorio' AND data = '$data' AND ora = '$ora' AND esame = '$esame'";
    return pg_query($GLOBALS['conn'], $sql);
}

// Ottenere i pazienti e gli esami disponibili
$pazienti = getPazienti();
$esami = getEsami();
$disponibilita = [];
$success = null;

// Controllare se la richiesta è POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['step']) && $_POST['step'] == '1') {
        // Fase 1: Ottenere disponibilità degli ambulatori
        $cf = $_POST['cf'];
        $esame = $_POST['esame'];
        $tipo_ambulatorio = $_POST['tipo_ambulatorio'];
        if ($tipo_ambulatorio == 'interno') {
            $disponibilita = getAmbulatoriInterni($esame);
        } else {
            $disponibilita = getAmbulatoriEsterni($esame);
        }
    } elseif (isset($_POST['step']) && $_POST['step'] == '2') {
        // Fase 2: Aggiungere la prenotazione
        $data_di_prenotazione = $_POST['data_di_prenotazione'];
        $regime = $_POST['regime'];
        $urgenza = $_POST['urgenza'];
        $paziente = $_POST['paziente'];
        $esame = $_POST['esame'];
        $ambulatorio = $_POST['ambulatorio'];
        $tipo_ambulatorio = $_POST['tipo_ambulatorio'];
        $data = $_POST['data'];
        $ora = $_POST['ora'];
        $medico_prescrittore = $_POST['medico_prescrittore'];
        $avvertenze = $_POST['avvertenze'];
        $success = addPrenotazione($data_di_prenotazione, $regime, $urgenza, $paziente, $esame, $ambulatorio, $tipo_ambulatorio, $data, $ora, $medico_prescrittore, $avvertenze);
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aggiungi Prenotazione</title>
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
        /* Stile per gli input di testo, date, time, select e textarea */
        input[type="text"], input[type="date"], input[type="time"], select, textarea {
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
        <h1>Aggiungi Prenotazione</h1>
        
        <!-- Messaggio di successo o errore dopo l'invio del modulo -->
        <?php if ($success !== null): ?>
            <div class="message <?php echo $success ? 'success' : 'error'; ?>">
                <?php echo $success ? 'Prenotazione aggiunta con successo!' : 'Errore nell\'aggiunta della prenotazione.'; ?>
            </div>
        <?php endif; ?>

        <!-- Controllo se il modulo è stato inviato e se siamo allo step 1 -->
        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['step']) && $_POST['step'] == '1'): ?>
            <!-- Modulo per il secondo step -->
            <form method="post">
                <!-- Campi hidden per mantenere lo stato del modulo -->
                <input type="hidden" name="step" value="2">
                <input type="hidden" name="paziente" value="<?php echo $_POST['cf']; ?>">
                <input type="hidden" name="esame" value="<?php echo $_POST['esame']; ?>">
                <input type="hidden" name="tipo_ambulatorio" value="<?php echo $_POST['tipo_ambulatorio']; ?>">
                
                <!-- Campo per la data di prenotazione -->
                <div class="form-group">
                    <label for="data_di_prenotazione">Data di Prenotazione:</label>
                    <input type="date" id="data_di_prenotazione" name="data_di_prenotazione" required>
                </div>
                
                <!-- Campo per il regime (pubblico o privato) -->
                <div class="form-group">
                    <label for="regime">Regime:</label>
                    <select id="regime" name="regime" required>
                        <option value="Pubblico">Pubblico</option>
                        <option value="Privato">Privato</option>
                    </select>
                </div>
                
                <!-- Campo per l'urgenza (verde, giallo, rosso) -->
                <div class="form-group">
                    <label for="urgenza">Urgenza:</label>
                    <select id="urgenza" name="urgenza" required>
                        <option value="Verde">Verde</option>
                        <option value="Giallo">Giallo</option>
                        <option value="Rosso">Rosso</option>
                    </select>
                </div>
                
                <!-- Campo per il medico prescrittore -->
                <div class="form-group">
                    <label for="medico">Medico Prescrittore:</label>
                    <input type="text" id="medico_prescrittore" name="medico_prescrittore">
                </div>
                
                <!-- Campo per le avvertenze -->
                <div class="form-group">
                    <label for="avvertenze">Avvertenze:</label>
                    <textarea id="avvertenze" name="avvertenze" rows="4"></textarea>
                </div>
                
                <!-- Campo per selezionare un ambulatorio disponibile -->
                <div class="form-group">
                    <label for="ambulatorio">Ambulatorio Disponibile:</label>
                    <select id="ambulatorio" name="ambulatorio" required>
                        <?php foreach ($disponibilita as $disp): ?>
                            <option value="<?php echo $_POST['tipo_ambulatorio'] == 'interno' ? $disp['ambulatorio_interno'] : $disp['ambulatorio_esterno']; ?>" data-data="<?php echo $disp['data']; ?>" data-ora="<?php echo $disp['ora']; ?>">
                                <?php echo $_POST['tipo_ambulatorio'] == 'interno' ? "Interno ID: {$disp['ambulatorio_interno']}, Data: {$disp['data']}, Ora: {$disp['ora']}" : "Esterno ID: {$disp['ambulatorio_esterno']}, Data: {$disp['data']}, Ora: {$disp['ora']}"; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Campi hidden per data e ora -->
                <input type="hidden" name="data" id="data">
                <input type="hidden" name="ora" id="ora">
                
                <!-- Bottone per confermare la prenotazione -->
                <button type="submit" class="btn">Conferma Prenotazione</button>
            </form>
            
            <!-- Script per gestire la selezione dell'ambulatorio -->
            <script>
                document.getElementById('ambulatorio').addEventListener('change', function() {
                    var selectedOption = this.options[this.selectedIndex];
                    document.getElementById('data').value = selectedOption.getAttribute('data-data');
                    document.getElementById('ora').value = selectedOption.getAttribute('data-ora');
                });
                // Trigger change event to set initial values
                document.getElementById('ambulatorio').dispatchEvent(new Event('change'));
            </script>
        
        <!-- Modulo per il primo step -->
        <?php else: ?>
            <form method="post">
                <input type="hidden" name="step" value="1">
                
                <!-- Campo per selezionare il paziente tramite codice fiscale -->
                <div class="form-group">
                    <label for="cf">CF Paziente:</label>
                    <select id="cf" name="cf" required>
                        <?php foreach ($pazienti as $paziente): ?>
                            <option value="<?php echo $paziente['cf']; ?>"><?php echo $paziente['nome'] . ' ' . $paziente['cognome']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Campo per selezionare il tipo di esame -->
                <div class="form-group">
                    <label for="esame">Tipo Esame:</label>
                    <select id="esame" name="esame" required>
                        <?php foreach ($esami as $esame): ?>
                            <option value="<?php echo $esame['codice']; ?>"><?php echo $esame['descrizione']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Campo per selezionare il tipo di ambulatorio (interno o esterno) -->
                <div class="form-group">
                    <label for="tipo_ambulatorio">Tipo Ambulatorio:</label>
                    <select id="tipo_ambulatorio" name="tipo_ambulatorio" required>
                        <option value="interno">Interno</option>
                        <option value="esterno">Esterno</option>
                    </select>
                </div>
                
                <!-- Bottone per verificare la disponibilità -->
                <button type="submit" class="btn">Verifica Disponibilità</button>
                <br>
                <br>
            </form>
        <?php endif; ?>

        <!-- Link per tornare alla pagina precedente -->
        <a href="prenotazione.php" class="btn" style="margin-top: 20px;">Torna Indietro</a>
    </div>
    <!-- Fine del contenitore principale -->
</body>
</html>
