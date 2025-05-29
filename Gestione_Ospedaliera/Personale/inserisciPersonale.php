 <?php
// Includere il file di configurazione del database
include '../config.php';

// Definisci le variabili e imposta i valori predefiniti
$nome = $cognome = $cf = $tipo = $ospedale = $reparto = $primario = $data_assunzione = $data_inizio = "";
$nome_err = $cognome_err = $cf_err = $tipo_err = $ospedale_err = $reparto_err = $primario_err = $data_assunzione_err = $data_inizio_err = "";
$success_msg = $error_msg = "";

// Processa i dati del modulo quando viene inviato
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validazione del nome
    if (empty(trim($_POST["nome"]))) {
        $nome_err = "Inserisci il nome.";
    } else {
        $nome = trim($_POST["nome"]);
    }

    // Validazione del cognome
    if (empty(trim($_POST["cognome"]))) {
        $cognome_err = "Inserisci il cognome.";
    } else {
        $cognome = trim($_POST["cognome"]);
    }

    // Validazione del codice fiscale
    if (empty(trim($_POST["cf"]))) {
        $cf_err = "Inserisci il codice fiscale.";
    } else {
        $cf = trim($_POST["cf"]);
        // Verifica se il codice fiscale esiste già
        $check_cf_query = "SELECT 1 FROM INFERMIERE WHERE cf = $1 UNION SELECT 1 FROM MEDICO WHERE cf = $1 UNION SELECT 1 FROM PERSONALE_AMMINISTRATIVO WHERE cf = $1";
        $check_cf_result = pg_query_params($conn, $check_cf_query, array($cf));
        if (pg_num_rows($check_cf_result) > 0) {
            $cf_err = "Codice fiscale già esistente.";
        }
    }

    // Validazione del tipo di personale
    if (empty(trim($_POST["tipo"]))) {
        $tipo_err = "Seleziona il tipo di personale.";
    } else {
        $tipo = trim($_POST["tipo"]);
    }

    // Validazione della data di inizio per Infermiere e Amministrativo
    if ($tipo == "infermiere" || $tipo == "amministrativo") {
        if (empty(trim($_POST["data_inizio"]))) {
            $data_inizio_err = "Inserisci la data di inizio.";
        } else {
            $data_inizio = trim($_POST["data_inizio"]);
        /*
            // Verifica che la data sia il primo giorno del mese solo per gli infermieri
            if ($tipo == "infermiere" && date('d', strtotime($data_inizio)) != '01') {
                $data_inizio_err = "La data di inizio deve essere il primo giorno del mese.";
            }
        */
        }

    }

    // Validazione della data di assunzione per Medico
    if ($tipo == "medico") {
        if (empty(trim($_POST["data_assunzione"]))) {
            $data_assunzione_err = "Inserisci la data di assunzione.";
        } else {
            $data_assunzione = trim($_POST["data_assunzione"]);
        }

        // Validazione del ruolo primario/viceprimario se tipo è medico
        if (empty(trim($_POST["primario"]))) {
            $primario_err = "Seleziona il ruolo.";
        } else {
            $primario = trim($_POST["primario"]);
        }

        // Controllo per l'unicità del primario nel reparto
        if ($primario == "primario") {
            if (empty(trim($_POST["ospedale"]))) {
                $ospedale_err = "Seleziona l'ospedale.";
            } else {
                $ospedale = trim($_POST["ospedale"]);
            }

            if (empty(trim($_POST["reparto"]))) {
                $reparto_err = "Seleziona il reparto.";
            } else {
                $reparto = trim($_POST["reparto"]);
            }

            if (empty($ospedale_err) && empty($reparto_err)) {
                $check_primario_query = "SELECT 1 FROM RUOLO_MEDICO IM 
                                         JOIN PRIMARIO P ON IM.medico = P.cf 
                                         WHERE IM.reparto_nome = $1 AND IM.reparto_ospedale = $2";
                $check_primario_result = pg_query_params($conn, $check_primario_query, array($reparto, $ospedale));

                if (pg_num_rows($check_primario_result) > 0) {
                    $primario_err = "Esiste già un primario per questo reparto.";
                }
            }
        }
    }

    // Validazione dell'ospedale
    if (empty(trim($_POST["ospedale"]))) {
        $ospedale_err = "Seleziona l'ospedale.";
    } else {
        $ospedale = trim($_POST["ospedale"]);
    }

    // Validazione del reparto
    if (empty(trim($_POST["reparto"]))) {
        $reparto_err = "Seleziona il reparto.";
    } else {
        $reparto = trim($_POST["reparto"]);
    }

    // Se non ci sono errori di validazione, procedi con l'inserimento nel database
    if (empty($nome_err) && empty($cognome_err) && empty($cf_err) && empty($tipo_err) && empty($ospedale_err) && empty($reparto_err) && ($tipo != "medico" || empty($primario_err)) && ($tipo == "medico" || empty($data_inizio_err))) {
        // Prepara l'istruzione INSERT per il personale
        $sql_personale = "";
        switch ($tipo) {
            case "infermiere":
                $sql_personale = "INSERT INTO INFERMIERE (cf, nome, cognome) VALUES ($1, $2, $3)";
                break;
            case "medico":
                $sql_personale = "INSERT INTO MEDICO (cf, nome, cognome, data_assunzione) VALUES ($1, $2, $3, $4)";
                break;
            case "amministrativo":
                $sql_personale = "INSERT INTO PERSONALE_AMMINISTRATIVO (cf, nome, cognome) VALUES ($1, $2, $3)";
                break;
            default:
                $error_msg = "Tipo di personale non valido.";
                exit();
        }

        $stmt_personale = pg_prepare($conn, "insert_personale", $sql_personale);
        $params_personale = ($tipo == "medico") ? array($cf, $nome, $cognome, $data_assunzione) : array($cf, $nome, $cognome);
        $result_personale = pg_execute($conn, "insert_personale", $params_personale);

        if ($result_personale) {
            // Prepara l'istruzione INSERT per l'associazione personale-ospedale-reparto
            $sql_associazione = "";
            switch ($tipo) {
                case "infermiere":
                    case "amministrativo":
                        $sql_associazione = "INSERT INTO RUOLO_" . strtoupper($tipo) . " (data_inizio, " . $tipo . ", reparto_nome, reparto_ospedale) VALUES ($1, $2, $3, $4)";
                        $stmt_associazione = pg_prepare($conn, "insert_associazione", $sql_associazione);
                        $result_associazione = pg_execute($conn, "insert_associazione", array($data_inizio, $cf, $reparto, $ospedale));
                        break;
                    case "medico":
                        $sql_associazione = "INSERT INTO RUOLO_MEDICO (data_inizio, medico, reparto_nome, reparto_ospedale) VALUES (CURRENT_DATE, $1, $2, $3)";
                        $stmt_associazione = pg_prepare($conn, "insert_associazione", $sql_associazione);
                        $result_associazione = pg_execute($conn, "insert_associazione", array($cf, $reparto, $ospedale));
                        break;
                    default:
                        $error_msg = "Tipo di personale non valido.";
                        exit();
            }

            if ($result_associazione) {
                if ($tipo == "medico") {
                    if ($primario == "primario") {
                        $sql_primario = "INSERT INTO PRIMARIO (cf) VALUES ($1)";
                    } else {
                        $sql_viceprimario = "INSERT INTO VICEPRIMARIO (cf) VALUES ($1)";
                    }

                    $stmt_ruolo = pg_prepare($conn, "insert_ruolo", $primario == "primario" ? $sql_primario : $sql_viceprimario);
                    $result_ruolo = pg_execute($conn, "insert_ruolo", array($cf));

                    if (!$result_ruolo) {
                        $error_msg = "Errore nell'inserimento del ruolo.";
                    }
                }

                $success_msg = "Personale inserito con successo!";
            } else {
                $error_msg = "Errore nell'inserimento dell'associazione.";
            }
        } else {
            $error_msg = "Errore nell'inserimento del personale.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserisci Nuovo Personale</title>
    <style>
        /* Stile per il corpo della pagina */
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f0f0f0;
        }
        /* Stile per il contenitore principale */
        .container {
            margin-top: 100px;
        }
        /* Stile per i gruppi di form */
        .form-group {
            margin: 20px;
        }
        /* Stile per gli input di testo, date e select */
        input[type="text"], input[type="date"], select {
            padding: 10px;
            width: 300px;
            margin: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        /* Stile per i pulsanti di submit */
        input[type="submit"] {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        /* Stile per i pulsanti di submit al passaggio del mouse */
        input[type="submit"]:hover {
            background-color: #218838;
        }
        /* Stile per i messaggi di errore */
        .error {
            color: red;
        }
        /* Stile per i messaggi di successo */
        .success {
            color: green;
        }
        /* Stile per il pulsante di ritorno */
        .back-button {
            margin-top: 20px;
        }
        /* Stile per il link di ritorno */
        .back-button a {
            text-decoration: none;
            color: #333;
            padding: 10px 20px;
            background-color: #ddd;
            border-radius: 4px;
        }
        /* Stile per il link di ritorno al passaggio del mouse */
        .back-button a:hover {
            background-color: #ccc;
        }
    </style>
    <script>
        // Funzione per aggiornare i reparti in base all'ospedale selezionato
        function updateReparti() {
            var ospedale = document.getElementById('ospedale').value;
            var repartoSelect = document.getElementById('reparto');
            repartoSelect.innerHTML = '<option value="">Seleziona...</option>'; // Resetta il menu a tendina dei reparti

            if (ospedale) {
                fetch('getReparti.php?ospedale=' + ospedale)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(reparto => {
                            var option = document.createElement('option');
                            option.value = reparto.nome;
                            option.textContent = reparto.nome;
                            repartoSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Errore:', error));
            }
        }

        // Funzione per mostrare/nascondere i campi aggiuntivi per medico e data di inizio
        function toggleFields() {
            var tipo = document.querySelector('select[name="tipo"]').value;
            var medicoFields = document.getElementById('medicoFields');
            var dataInizioFields = document.getElementById('dataInizioFields');

            if (tipo === 'medico') {
                medicoFields.style.display = 'block';
                dataInizioFields.style.display = 'none'; // Nasconde la data di inizio per gli altri tipi
            } else {
                medicoFields.style.display = 'none';
                dataInizioFields.style.display = 'block'; // Mostra la data di inizio per infermieri e amministrativi
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Inserisci Nuovo Personale</h2>
        <?php
        // Mostrare messaggio di successo se presente
        if (!empty($success_msg)) {
            echo '<p class="success">' . $success_msg . '</p>';
        }
        // Mostrare messaggio di errore se presente
        if (!empty($error_msg)) {
            echo '<p class="error">' . $error_msg . '</p>';
        }
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
                <label>Ospedale:</label>
                <select name="ospedale" id="ospedale" onchange="updateReparti()">
                    <option value="">Seleziona...</option>
                    <?php
                    // Recupera elenco ospedali dal database
                    $ospedaliQuery = "SELECT codice, nome FROM OSPEDALE";
                    $ospedaliResult = pg_query($conn, $ospedaliQuery);
                    if ($ospedaliResult) {
                        while ($row = pg_fetch_assoc($ospedaliResult)) {
                            echo "<option value='" . $row['codice'] . "' " . ($ospedale == $row['codice'] ? 'selected' : '') . ">" . $row['nome'] . "</option>";
                        }
                    } else {
                        echo "Errore nella query: " . pg_last_error($conn);
                    }
                    ?>
                </select>
                <span class="error"><?php echo $ospedale_err; ?></span>
            </div>
            <div class="form-group">
                <label>Reparto:</label>
                <select name="reparto" id="reparto">
                    <option value="">Seleziona...</option>
                    <?php
                    // Se è stato selezionato un ospedale, recupera i reparti corrispondenti
                    if (!empty($ospedale)) {
                        $repartiResult = getReparti($conn, $ospedale);
                        while ($row = pg_fetch_assoc($repartiResult)) {
                            echo "<option value='" . $row['nome'] . "' " . ($reparto == $row['nome'] ? 'selected' : '') . ">" . $row['nome'] . "</option>";
                        }
                    }
                    ?>
                </select>
                <span class="error"><?php echo $reparto_err; ?></span>
            </div>
            <div class="form-group">
                <label>Nome:</label>
                <input type="text" name="nome" value="<?php echo $nome; ?>">
                <span class="error"><?php echo $nome_err; ?></span>
            </div>
            <div class="form-group">
                <label>Cognome:</label>
                <input type="text" name="cognome" value="<?php echo $cognome; ?>">
                <span class="error"><?php echo $cognome_err; ?></span>
            </div>
            <div class="form-group">
                <label>Codice Fiscale:</label>
                <input type="text" name="cf" value="<?php echo $cf; ?>">
                <span class="error"><?php echo $cf_err; ?></span>
            </div>
            <div class="form-group">
                <label>Tipo di Personale:</label>
                <select name="tipo" onchange="toggleFields()">
                    <option value="">Seleziona...</option>
                    <option value="infermiere" <?php echo ($tipo == 'infermiere') ? 'selected' : ''; ?>>Infermiere</option>
                    <option value="medico" <?php echo ($tipo == 'medico') ? 'selected' : ''; ?>>Medico</option>
                    <option value="amministrativo" <?php echo ($tipo == 'amministrativo') ? 'selected' : ''; ?>>Personale Amministrativo</option>
                </select>
                <span class="error"><?php echo $tipo_err; ?></span>
            </div>
            <div id="dataInizioFields" style="display: <?php echo ($tipo == 'medico') ? 'none' : 'block'; ?>;">
                <div class="form-group">
                    <label>Data di Inizio:</label>
                    <input type="date" name="data_inizio" value="<?php echo $data_inizio; ?>">
                    <span class="error"><?php echo $data_inizio_err; ?></span>
                </div>
            </div>
            <div id="medicoFields" style="display: <?php echo ($tipo == 'medico') ? 'block' : 'none'; ?>;">
                <div class="form-group">
                    <label>Ruolo:</label>
                    <select name="primario">
                        <option value="">Seleziona...</option>
                        <option value="primario" <?php echo ($primario == 'primario') ? 'selected' : ''; ?>>Primario</option>
                        <option value="viceprimario" <?php echo ($primario == 'viceprimario') ? 'selected' : ''; ?>>Viceprimario</option>
                    </select>
                    <span class="error"><?php echo $primario_err; ?></span>
                </div>
                <div class="form-group">
                    <label>Data di Assunzione:</label>
                    <input type="date" name="data_assunzione" value="<?php echo $data_assunzione; ?>">
                    <span class="error"><?php echo $data_assunzione_err; ?></span>
                </div>
            </div>
            <div>
                <input type="submit" value="Inserisci">
            </div>
        </form>
        <div class="back-button">
            <a href="personale.php">Torna a Personale</a>
        </div>
    </div>
</body>
</html>


<?php
// Funzione per ottenere i reparti in base all'ospedale selezionato
function getReparti($conn, $ospedale_id) {
    $sql = "SELECT nome FROM REPARTO WHERE ospedale = $1";
    return pg_query_params($conn, $sql, array($ospedale_id));
}

// Controlla se l'ID dell'ospedale è stato passato nella richiesta GET
if (isset($_GET['ospedale'])) {
    $ospedale = intval($_GET['ospedale']);
    $repartiResult = getReparti($conn, $ospedale);

    // Inizializza un array per memorizzare i reparti
    $reparti = array();
    while ($row = pg_fetch_assoc($repartiResult)) {
        $reparti[] = $row;
    }

    // Imposta l'intestazione del contenuto come JSON
    header('Content-Type: application/json');
    // Codifica l'array dei reparti in formato JSON e invialo come risposta
    echo json_encode($reparti);
}
?>
