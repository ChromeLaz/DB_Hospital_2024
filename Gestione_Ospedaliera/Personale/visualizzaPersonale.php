<?php
// Includere il file di configurazione del database
include '../config.php';

// Inizializzare variabili vuote per ospedale, reparto, tipo e relativi messaggi di errore
$ospedale = $reparto = $tipo = "";
$ospedale_err = $reparto_err = $tipo_err = "";
$personale = [];

// Controllare se la richiesta è POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ottenere i valori dai campi del modulo
    $ospedale = trim($_POST["ospedale"]);
    $reparto = trim($_POST["reparto"]);
    $tipo = trim($_POST["tipo"]);

    // Validare i campi del modulo
    if (empty($ospedale)) {
        $ospedale_err = "Seleziona l'ospedale.";
    }

    if (empty($reparto)) {
        $reparto_err = "Seleziona il reparto.";
    }

    if (empty($tipo)) {
        $tipo_err = "Seleziona il tipo di personale.";
    }

    // Se non ci sono errori, eseguire la query per ottenere il personale
    if (empty($ospedale_err) && empty($reparto_err) && empty($tipo_err)) {
        $personaleQuery = "";
        switch ($tipo) {
            case "infermiere":
                $personaleQuery = "
         SELECT I.cf, I.nome, I.cognome, R.nome AS reparto, O.nome AS ospedale, II.data_inizio
        FROM INFERMIERE I
        JOIN RUOLO_INFERMIERE II ON I.cf = II.infermiere
        JOIN REPARTO R ON II.reparto_nome = R.nome AND II.reparto_ospedale = R.ospedale
        JOIN OSPEDALE O ON R.ospedale = O.codice
        WHERE R.ospedale = $1 AND R.nome = $2";
    break;
                break;
            case "medico":
                $personaleQuery = "
                    SELECT M.cf, M.nome, M.cognome, M.data_assunzione, R.nome AS reparto, O.nome AS ospedale,
                           CASE WHEN P.cf IS NOT NULL THEN 'primario' ELSE 'viceprimario' END AS ruolo
                    FROM MEDICO M
                    JOIN RUOLO_MEDICO IM ON M.cf = IM.medico
                    JOIN REPARTO R ON IM.reparto_nome = R.nome AND IM.reparto_ospedale = R.ospedale
                    JOIN OSPEDALE O ON R.ospedale = O.codice
                    LEFT JOIN PRIMARIO P ON M.cf = P.cf
                    LEFT JOIN VICEPRIMARIO V ON M.cf = V.cf
                    WHERE R.ospedale = $1 AND R.nome = $2";
                break;
            case "amministrativo":
                $personaleQuery = "
                    SELECT PA.cf, PA.nome, PA.cognome, R.nome AS reparto, O.nome AS ospedale, IA.data_inizio
                    FROM PERSONALE_AMMINISTRATIVO PA
                    JOIN RUOLO_AMMINISTRATIVO IA ON PA.cf = IA.amministrativo
                    JOIN REPARTO R ON IA.reparto_nome = R.nome AND IA.reparto_ospedale = R.ospedale
                    JOIN OSPEDALE O ON R.ospedale = O.codice
                    WHERE R.ospedale = $1 AND R.nome = $2";
                break;
            default:
                $error_msg = "Tipo di personale non valido.";
                exit();
        }

        // Preparare ed eseguire la query per ottenere il personale
        $stmt_personale = pg_prepare($conn, "personale_query", $personaleQuery);
        $result_personale = pg_execute($conn, "personale_query", array($ospedale, $reparto));
        if ($result_personale) {
            while ($row = pg_fetch_assoc($result_personale)) {
                $personale[] = $row;
            }
        } else {
            $error_msg = "Errore nella query: " . pg_last_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizza Personale</title>
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
        /* Stile per le tabelle */
        table {
            margin: 0 auto;
            border-collapse: collapse;
            width: 80%;
        }
        /* Stile per le celle delle tabelle */
        table, th, td {
            border: 1px solid #ddd;
        }
        /* Stile per le celle di intestazione delle tabelle */
        th, td {
            padding: 10px;
            text-align: left;
        }
        /* Stile per le celle di intestazione delle tabelle */
        th {
            background-color: #f2f2f2;
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
    </script>
</head>
<body>
    <div class="container">
        <h2>Visualizza Personale</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Ospedale:</label>
                <select name="ospedale" id="ospedale" onchange="updateReparti()">
                    <option value="">Seleziona...</option>
                    <?php
                    // Recuperare la lista degli ospedali dal database
                    $ospedaliQuery = "SELECT codice, nome FROM OSPEDALE";
                    $ospedaliResult = pg_query($conn, $ospedaliQuery);
                    if ($ospedaliResult) {
                        // Popolare il menu a tendina con i risultati
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
            <div class="form-group">
                <label>Tipo di Personale:</label>
                <select name="tipo">
                    <option value="">Seleziona...</option>
                    <option value="infermiere" <?php echo ($tipo == 'infermiere') ? 'selected' : ''; ?>>Infermiere</option>
                    <option value="medico" <?php echo ($tipo == 'medico') ? 'selected' : ''; ?>>Medico</option>
                    <option value="amministrativo" <?php echo ($tipo == 'amministrativo') ? 'selected' : ''; ?>>Personale Amministrativo</option>
                </select>
                <span class="error"><?php echo $tipo_err; ?></span>
            </div>
            <div>
                <input type="submit" value="Visualizza">
            </div>
        </form>
        <?php if (!empty($personale)): ?>
    <h3>Elenco del Personale</h3>
    <table>
        <tr>
            <th>Codice Fiscale</th>
            <th>Nome</th>
            <th>Cognome</th>
            <?php if ($tipo == "medico"): ?>
                <th>Data di Assunzione</th>
                <th>Ruolo</th>
            <?php endif; ?>
            <?php if ($tipo == "amministrativo" || $tipo == "infermiere"): ?>
                <th>Data di Inizio</th>
            <?php endif; ?>
            <th>Reparto</th>
            <th>Ospedale</th>
        </tr>
        <?php foreach ($personale as $p): ?>
            <tr>
                <td><?php echo $p['cf']; ?></td>
                <td><?php echo $p['nome']; ?></td>
                <td><?php echo $p['cognome']; ?></td>
                <?php if ($tipo == "medico"): ?>
                    <td><?php echo $p['data_assunzione']; ?></td>
                    <td><?php echo $p['ruolo']; ?></td>
                <?php endif; ?>
                <?php if ($tipo == "amministrativo"): ?>
                    <td><?php echo $p['data_inizio']; ?></td>
                <?php endif; ?>
                <?php if ($tipo == "infermiere"): ?>
                    <td><?php echo $p['data_inizio']; ?></td>
                <?php endif; ?>
                <td><?php echo $p['reparto']; ?></td>
                <td><?php echo $p['ospedale']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
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
