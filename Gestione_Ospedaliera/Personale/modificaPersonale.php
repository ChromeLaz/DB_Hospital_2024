<?php
// Includere il file di configurazione del database
include '../config.php';

// Definire variabili e inizializzare con valori vuoti
$cf_search = $cf = $nome = $cognome = $tipo = $ospedale = $reparto = $primario = $data_assunzione = "";
$cf_search_err = $cf_err = $nome_err = $cognome_err = $tipo_err = $ospedale_err = $reparto_err = $primario_err = $data_assunzione_err = "";
$success_msg = $error_msg = "";

// Recuperare i dettagli del personale se cf_search è passato
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["cf_search"])) {
    $cf_search = trim($_POST["cf_search"]);
    $sql_personale = "
        SELECT cf, nome, cognome, NULL AS data_assunzione, 'infermiere' AS tipo FROM INFERMIERE WHERE cf = $1
        UNION ALL
        SELECT cf, nome, cognome, data_assunzione, 'medico' AS tipo FROM MEDICO WHERE cf = $1
        UNION ALL
        SELECT cf, nome, cognome, NULL AS data_assunzione, 'amministrativo' AS tipo FROM PERSONALE_AMMINISTRATIVO WHERE cf = $1";
    $result_personale = pg_query_params($conn, $sql_personale, array($cf_search));

    if ($result_personale && pg_num_rows($result_personale) > 0) {
        $row = pg_fetch_assoc($result_personale);
        $cf = $row['cf'];
        $nome = $row['nome'];
        $cognome = $row['cognome'];
        $tipo = $row['tipo'];
        $data_assunzione = $row['data_assunzione'];

        // Recuperare i dettagli dell'RUOLO
        if ($tipo == "infermiere") {
            $sql_RUOLO = "SELECT reparto_nome, reparto_ospedale FROM RUOLO_INFERMIERE WHERE infermiere = $1";
        } elseif ($tipo == "medico") {
            $sql_RUOLO = "SELECT reparto_nome, reparto_ospedale FROM RUOLO_MEDICO WHERE medico = $1";
        } else {
            $sql_RUOLO = "SELECT reparto_nome, reparto_ospedale FROM RUOLO_AMMINISTRATIVO WHERE amministrativo = $1";
        }
        $result_RUOLO = pg_query_params($conn, $sql_RUOLO, array($cf));
        if ($result_RUOLO && pg_num_rows($result_RUOLO) > 0) {
            $row_RUOLO = pg_fetch_assoc($result_RUOLO);
            $ospedale = $row_RUOLO['reparto_ospedale'];
            $reparto = $row_RUOLO['reparto_nome'];
        }
    } else {
        $cf_search_err = "Codice fiscale non trovato.";
    }
}

// Processare i dati del modulo quando viene inviato per l'aggiornamento
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["cf"])) {
    // Validare e aggiornare i campi
    $ospedale = trim($_POST["ospedale"]);
    $reparto = trim($_POST["reparto"]);
    $primario = isset($_POST["primario"]) ? trim($_POST["primario"]) : "";
    $tipo = trim($_POST["tipo"]);
    $cf = trim($_POST["cf"]);
    $nome = trim($_POST["nome"]);
    $cognome = trim($_POST["cognome"]);
    $data_assunzione = trim($_POST["data_assunzione"]);

    if (empty($ospedale)) {
        $ospedale_err = "Seleziona l'ospedale.";
    }
    if (empty($reparto)) {
        $reparto_err = "Seleziona il reparto.";
    }

    // Verifica se esiste già un altro primario nello stesso reparto
    if ($primario == "primario") {
        $check_primario_query = "SELECT 1 FROM RUOLO_MEDICO IM 
                                 JOIN PRIMARIO P ON IM.medico = P.cf 
                                 WHERE IM.reparto_nome = $1 AND IM.reparto_ospedale = $2 AND P.cf <> $3";
        $check_primario_result = pg_query_params($conn, $check_primario_query, array($reparto, $ospedale, $cf));

        if (pg_num_rows($check_primario_result) > 0) {
            $primario_err = "Esiste già un primario per questo reparto.";
        }
    }

    // Aggiornare il database se non ci sono errori
    if (empty($ospedale_err) && empty($reparto_err) && empty($primario_err)) {
        // Preparare l'istruzione UPDATE per il personale
        if ($tipo == "infermiere") {
            $sql_personale = "UPDATE INFERMIERE SET nome = $1, cognome = $2 WHERE cf = $3";
        } elseif ($tipo == "medico") {
            $sql_personale = "UPDATE MEDICO SET nome = $1, cognome = $2, data_assunzione = $3 WHERE cf = $4";
        } else {
            $sql_personale = "UPDATE PERSONALE_AMMINISTRATIVO SET nome = $1, cognome = $2 WHERE cf = $3";
        }

        // Preparare l'istruzione SQL per l'aggiornamento
        $stmt_personale = pg_prepare($conn, "update_personale", $sql_personale);
        $params_personale = ($tipo == "medico") ? array($nome, $cognome, $data_assunzione, $cf) : array($nome, $cognome, $cf);
        $result_personale = pg_execute($conn, "update_personale", $params_personale);

        if ($result_personale) {
            // Aggiornare il ruolo se necessario
            $sql_associazione = "";
            switch ($tipo) {
                case "infermiere":
                    $sql_associazione = "UPDATE RUOLO_INFERMIERE SET reparto_nome = $1, reparto_ospedale = $2 WHERE infermiere = $3";
                    break;
                case "medico":
                    $sql_associazione = "UPDATE RUOLO_MEDICO SET reparto_nome = $1, reparto_ospedale = $2 WHERE medico = $3";
                    break;
                case "amministrativo":
                    $sql_associazione = "UPDATE RUOLO_AMMINISTRATIVO SET reparto_nome = $1, reparto_ospedale = $2 WHERE amministrativo = $3";
                    break;
                default:
                    $error_msg = "Tipo di personale non valido.";
                    exit();
            }

            $stmt_associazione = pg_prepare($conn, "update_associazione", $sql_associazione);
            $result_associazione = pg_execute($conn, "update_associazione", array($reparto, $ospedale, $cf));

            if ($result_associazione) {
                // Aggiornare il ruolo primario/viceprimario se necessario
                if ($tipo == "medico") {
                    $sql_delete_roles_primario = "DELETE FROM PRIMARIO WHERE cf = $1";
                    $sql_delete_roles_viceprimario = "DELETE FROM VICEPRIMARIO WHERE cf = $1";
                    pg_query_params($conn, $sql_delete_roles_primario, array($cf));
                    pg_query_params($conn, $sql_delete_roles_viceprimario, array($cf));

                    if ($primario == "primario") {
                        $sql_primario = "INSERT INTO PRIMARIO (cf) VALUES ($1)";
                        pg_query_params($conn, $sql_primario, array($cf));
                    } elseif ($primario == "viceprimario") {
                        $sql_viceprimario = "INSERT INTO VICEPRIMARIO (cf) VALUES ($1)";
                        pg_query_params($conn, $sql_viceprimario, array($cf));
                    }
                }

                // Messaggio di successo
                $success_msg = "Personale aggiornato con successo!";
            } else {
                $error_msg = "Qualcosa è andato storto nell'aggiornamento dell'associazione. Si prega di riprovare più tardi.";
            }
        } else {
            $error_msg = "Errore nell'aggiornamento del personale.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Personale</title>
    <style>
        /* Stile per il corpo della pagina */
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f0f0f0;
        }
        /* Stile per il contenitore principale */
        .container {
            margin-top: 50px;
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
</head>
<body>
    <div class="container">
        <h2>Modifica Personale</h2>
        <?php
        // Mostrare il messaggio di successo se presente
        if (!empty($success_msg)) {
            echo '<p class="success">' . $success_msg . '</p>';
        }
        // Mostrare il messaggio di errore se presente
        if (!empty($error_msg)) {
            echo '<p class="error">' . $error_msg . '</p>';
        }
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Cerca per Codice Fiscale:</label>
                <input type="text" name="cf_search" value="<?php echo $cf_search; ?>">
                <input type="submit" value="Cerca">
                <span class="error"><?php echo $cf_search_err; ?></span>
            </div>
        </form>
        <?php if (!empty($cf)): ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <input type="hidden" name="cf" value="<?php echo $cf; ?>">
                <input type="hidden" name="tipo" value="<?php echo $tipo; ?>">
                <div class="form-group">
                    <label>Nome:</label>
                    <input type="text" name="nome" value="<?php echo $nome; ?>">
                </div>
                <div class="form-group">
                    <label>Cognome:</label>
                    <input type="text" name="cognome" value="<?php echo $cognome; ?>">
                </div>
                <div class="form-group">
                    <label>Codice Fiscale:</label>
                    <input type="text" name="cf_display" value="<?php echo $cf; ?>" readonly>
                </div>
                <?php if ($tipo == "medico"): ?>
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
                    </div>
                <?php endif; ?>
                <div class="form-group">
                    <label>Ospedale:</label>
                    <select name="ospedale">
                        <option value="">Seleziona...</option>
                        <?php
                        // Recuperare la lista degli ospedali dal database
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
                    <select name="reparto">
                        <option value="">Seleziona...</option>
                        <?php
                        // Se è stato selezionato un ospedale, recuperare i reparti corrispondenti
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
                <div>
                    <input type="submit" value="Modifica">
                </div>
            </form>
        <?php endif; ?>
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
?>
