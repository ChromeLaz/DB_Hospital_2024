<?php
// Include il file di configurazione per la connessione al database
include '../config.php';

// Inizializza le variabili per i messaggi di successo ed errore
$successMessage = "";
$errorMessage = "";

// Controlla se il metodo della richiesta è POST e se è stato passato il parametro 'step'
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['step']) && $_POST['step'] == "3") {
    // Ottiene i dati inviati dal modulo
    $data = $_POST['data'];
    $orario_inizio = $_POST['orario_inizio'];
    $orario_fine = $_POST['orario_fine'];
    $cf = $_POST['cf'];
    list($PRONTOSOCCORSO, $ospedale) = explode('-', $_POST['PRONTOSOCCORSO']);
    $tipo = $_POST['tipo'];

    // Costruisce la query per verificare se esiste già un turno per l'infermiere o il medico
    if ($tipo == 'infermiere') {
        $sql_check = "SELECT * FROM TURNO_INFERMIERE WHERE data=$1 AND cf=$2 AND PRONTOSOCCORSO=$3 AND ospedale=$4";
    } else {
        $sql_check = "SELECT * FROM TURNO_MEDICO WHERE data=$1 AND cf=$2 AND PRONTOSOCCORSO=$3 AND ospedale=$4";
    }

    // Esegue la query di controllo
    $result_check = pg_query_params($conn, $sql_check, array($data, $cf, $PRONTOSOCCORSO, $ospedale));

    // Se esiste già un turno, mostra un messaggio di errore
    if (pg_num_rows($result_check) > 0) {
        $errorMessage = "Errore: Il turno esiste già per questa data e personale.";
    } else {
        // Costruisce la query di inserimento in base al tipo di personale
        if ($tipo == 'infermiere') {
            $sql_insert = "INSERT INTO TURNO_INFERMIERE (data, orario_inizio, orario_fine, cf, PRONTOSOCCORSO, ospedale) VALUES ($1, $2, $3, $4, $5, $6)";
        } else {
            $sql_insert = "INSERT INTO TURNO_MEDICO (data, orario_inizio, orario_fine, cf, PRONTOSOCCORSO, ospedale) VALUES ($1, $2, $3, $4, $5, $6)";
        }

        // Esegue la query di inserimento e mostra un messaggio di successo o errore
        $result_insert = pg_query_params($conn, $sql_insert, array($data, $orario_inizio, $orario_fine, $cf, $PRONTOSOCCORSO, $ospedale));
        if ($result_insert) {
            $successMessage = "Turno aggiunto con successo.";
        } else {
            $errorMessage = "Errore: " . pg_last_error($conn);
        }
    }
}

// Query per ottenere i PRONTOSOCCORSOdisponibili
$sql_PRONTOSOCCORSO= "SELECT nome, ospedale FROM PRONTOSOCCORSO";
$result_PRONTOSOCCORSO= pg_query($conn, $sql_PRONTOSOCCORSO); // Esegue la query e memorizza il risultato

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aggiungi Turno</title>
    <style>
        /* Stile per il corpo della pagina */
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f0f0f0;
        }
        /* Stile per il form */
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
        /* Stile per gli input e i select del form */
        form input, form select {
            width: 100%;
            padding: 8px;
            margin: 8px 0;
            box-sizing: border-box;
        }
        /* Stile per il pulsante di invio */
        form input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        form input[type="submit"]:hover {
            background-color: #45a049;
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
    </style>
    <script>
        function aggiornaPersonale() {
            var tipo = document.getElementById("tipo").value;
            var PRONTOSOCCORSO= document.getElementById("PRONTOSOCCORSO").value;
            var personaleSelect = document.getElementById("cf");

            personaleSelect.innerHTML = '<option value="">Seleziona...</option>';

            if (PRONTOSOCCORSO && tipo) {
                fetch('aggiungiTurno.php?step=personale&PRONTOSOCCORSO=' + PRONTOSOCCORSO + '&tipo=' + tipo)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(personale => {
                            var option = document.createElement('option');
                            option.value = personale.cf;
                            option.textContent = personale.nome + ' ' + personale.cognome;
                            personaleSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Errore:', error));
            }
        }
    </script>
</head>
<body>
    <h2>Aggiungi Turno</h2>
    <!-- Mostra i messaggi di successo o errore se presenti -->
    <?php if (!empty($successMessage)): ?>
        <p class="success"><?php echo $successMessage; ?></p>
    <?php endif; ?>
    <?php if (!empty($errorMessage)): ?>
        <p class="error"><?php echo $errorMessage; ?></p>
    <?php endif; ?>

    <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['step']) && $_POST['step'] == "2"): ?>
        <form method="post" action="aggiungiTurno.php">
            <input type="hidden" name="step" value="3">
            <input type="hidden" name="PRONTOSOCCORSO" value="<?php echo $_POST['PRONTOSOCCORSO']; ?>">
            <input type="hidden" name="tipo" value="<?php echo $_POST['tipo']; ?>">

            <div class="form-group">
                <label for="cf">Personale Disponibile:</label>
                <select id="cf" name="cf" required>
                    <option value="">Seleziona...</option>
                    <?php
                    $tipo = $_POST['tipo'];
                    list($PRONTOSOCCORSO, $ospedale) = explode('-', $_POST['PRONTOSOCCORSO']);

                    if ($tipo == 'infermiere') {
                        $sql_personale = "SELECT cf, nome, cognome 
                                          FROM INFERMIERE 
                                          WHERE cf IN (
                                              SELECT infermiere 
                                              FROM RUOLO_INFERMIERE 
                                              WHERE reparto_ospedale = $1
                                          ) 
                                          AND cf NOT IN (
                                              SELECT cf 
                                              FROM TURNO_INFERMIERE 
                                              WHERE data = $2 AND PRONTOSOCCORSO= $3 AND ospedale = $4
                                          )";
                    } else {
                        $sql_personale = "SELECT cf, nome, cognome 
                                          FROM MEDICO 
                                          WHERE cf IN (
                                              SELECT medico 
                                              FROM RUOLO_MEDICO 
                                              WHERE reparto_ospedale = $1
                                          ) 
                                          AND cf NOT IN (
                                              SELECT cf 
                                              FROM TURNO_MEDICO 
                                              WHERE data = $2 AND PRONTOSOCCORSO= $3 AND ospedale = $4
                                          )";
                    }

                    $result_personale = pg_query_params($conn, $sql_personale, array($ospedale, $_POST['data'], $PRONTOSOCCORSO, $ospedale));

                    while ($row = pg_fetch_assoc($result_personale)) {
                        echo "<option value='{$row['cf']}'>{$row['nome']} {$row['cognome']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="data">Data:</label>
                <input type="date" id="data" name="data" required>
            </div>

            <div class="form-group">
                <label for="orario_inizio">Orario di Inizio:</label>
                <input type="time" id="orario_inizio" name="orario_inizio" required>
            </div>

            <div class="form-group">
                <label for="orario_fine">Orario di Fine:</label>
                <input type="time" id="orario_fine" name="orario_fine" required>
            </div>

            <input type="submit" value="Aggiungi Turno">
        </form>
    <?php else: ?>
        <form method="post" action="aggiungiTurno.php">
            <input type="hidden" name="step" value="2">
            <div class="form-group">
                <label for="PRONTOSOCCORSO">PRONTOSOCCORSO:</label>
                <select id="PRONTOSOCCORSO" name="PRONTOSOCCORSO" required>
                    <?php
                    // Popola il select dei PRONTOSOCCORSOdisponibili
                    while ($row = pg_fetch_assoc($result_PRONTOSOCCORSO)) {
                        echo "<option value='{$row['nome']}-{$row['ospedale']}'>{$row['nome']} (Ospedale: {$row['ospedale']})</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="tipo">Tipo:</label>
                <select id="tipo" name="tipo" onchange="aggiornaPersonale()" required>
                    <option value="">Seleziona...</option>
                    <option value="infermiere">Infermiere</option>
                    <option value="medico">Medico</option>
                </select>
            </div>

            <input type="submit" value="Avanti">
        </form>
    <?php endif; ?>

    <div class="back-button">
        <a href="turniProntoSoccorso.php">Torna ai Turni</a> <!-- Pulsante per tornare alla pagina dei turni -->
    </div>
</body>
</html>

<?php
// Funzione per ottenere il personale disponibile
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['step']) && $_GET['step'] == "personale") {
    list($PRONTOSOCCORSO, $ospedale) = explode('-', $_GET['PRONTOSOCCORSO']);
    $tipo = $_GET['tipo'];

    if ($tipo == 'infermiere') {
        $sql_personale = "SELECT cf, nome, cognome 
                          FROM INFERMIERE 
                          WHERE cf IN (
                              SELECT infermiere 
                              FROM RUOLO_INFERMIERE 
                              WHERE reparto_ospedale = $1
                          ) 
                          AND cf NOT IN (
                              SELECT cf 
                              FROM TURNO_INFERMIERE 
                              WHERE data = $2 AND PRONTOSOCCORSO= $3 AND ospedale = $4
                          )";
    } else {
        $sql_personale = "SELECT cf, nome, cognome 
                          FROM MEDICO 
                          WHERE cf IN (
                              SELECT medico 
                              FROM RUOLO_MEDICO 
                              WHERE reparto_ospedale = $1
                          ) 
                          AND cf NOT IN (
                              SELECT cf 
                              FROM TURNO_MEDICO 
                              WHERE data = $2 AND PRONTOSOCCORSO= $3 AND ospedale = $4
                          )";
    }

    $result_personale = pg_query_params($conn, $sql_personale, array($ospedale, date('Y-m-d'), $PRONTOSOCCORSO, $ospedale));

    $personale = array();
    while ($row = pg_fetch_assoc($result_personale)) {
        $personale[] = $row;
    }

    echo json_encode($personale);
}

// Chiude la connessione al database
pg_close($conn);
?>
