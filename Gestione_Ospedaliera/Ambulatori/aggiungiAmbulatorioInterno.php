<?php
// Include il file di configurazione per la connessione al database
include '../config.php';

// Inizializza le variabili
$aggiunto = false;
$result_stanze = null;

// Controlla se il form è stato inviato
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Verifica che siano stati selezionati ospedale e stanza
    if (isset($_POST['stanza']) && isset($_POST['ospedale'])) {
        $stanza = $_POST['stanza'];
        $ospedale = $_POST['ospedale'];

        // Controlla se la stanza è già un ambulatorio interno per l'ospedale selezionato
        $sql_check_ambulatorio = "SELECT * FROM AMBULATORIO_INTERNO ai 
                                  JOIN STANZA s ON ai.stanza = s.id
                                  WHERE s.id = $stanza AND s.reparto_ospedale = $ospedale";
        $result_check_ambulatorio = pg_query($conn, $sql_check_ambulatorio);
        if (!$result_check_ambulatorio) {
            die("Query failed: " . pg_last_error($conn));
        }

        // Controlla se la stanza è una sala operatoria per l'ospedale selezionato
        $sql_check_sala_operatoria = "SELECT * FROM SALA_OPERATORIA so 
                                      JOIN STANZA s ON so.stanza = s.id
                                      WHERE s.id = $stanza AND s.reparto_ospedale = $ospedale";
        $result_check_sala_operatoria = pg_query($conn, $sql_check_sala_operatoria);
        if (!$result_check_sala_operatoria) {
            die("Query failed: " . pg_last_error($conn));
        }

        // Verifica se la stanza è già un ambulatorio interno o una sala operatoria per l'ospedale selezionato
        if (pg_num_rows($result_check_ambulatorio) > 0) {
            echo "Errore: La stanza selezionata è già un ambulatorio interno per questo ospedale.";
        } elseif (pg_num_rows($result_check_sala_operatoria) > 0) {
            echo "Errore: La stanza selezionata è una sala operatoria per questo ospedale.";
        } else {
            // Costruisce la query di inserimento per aggiungere un ambulatorio interno
            $sql_insert = "INSERT INTO AMBULATORIO_INTERNO (stanza) VALUES ($stanza)";

            // Esegue la query di inserimento e mostra un messaggio di successo o errore
            if (pg_query($conn, $sql_insert)) {
                $aggiunto = true;
                $messaggio_successo = "Ambulatorio Interno aggiunto con successo.";
            } else {
                echo "Errore: " . pg_last_error($conn);
            }
        }
    }
}

// Query per ottenere gli ospedali
$sql_ospedali = "SELECT codice, nome FROM OSPEDALE";
$result_ospedali = pg_query($conn, $sql_ospedali);
if (!$result_ospedali) {
    die("Query failed: " . pg_last_error($conn));
}

// Se è stato selezionato un ospedale, ottenere le stanze disponibili per quell'ospedale
if (isset($_POST['ospedale'])) {
    $selected_ospedale = $_POST['ospedale'];

    // Query per ottenere le stanze disponibili che non sono né ambulatori interni né sale operatorie per l'ospedale selezionato
    $sql_stanze = "SELECT s.id, s.numero, s.piano, s.reparto_ospedale 
                   FROM STANZA s 
                   LEFT JOIN AMBULATORIO_INTERNO ai ON s.id = ai.stanza 
                   LEFT JOIN SALA_OPERATORIA so ON s.id = so.stanza 
                   WHERE s.reparto_ospedale = $selected_ospedale 
                   AND ai.stanza IS NULL AND so.stanza IS NULL";
    
    $result_stanze = pg_query($conn, $sql_stanze);
    if (!$result_stanze) {
        die("Query failed: " . pg_last_error($conn));
    }
}

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aggiungi Ambulatorio Interno</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f0f0f0;
        }
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
        form input, form select {
            width: 100%;
            padding: 8px;
            margin: 8px 0;
            box-sizing: border-box;
        }
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
</head>
<body>
    <h2>Aggiungi Ambulatorio Interno</h2>
    <?php if ($aggiunto && isset($messaggio_successo)) : ?>
        <p><?php echo htmlspecialchars($messaggio_successo); ?></p>
    <?php endif; ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        Ospedale: 
        <select name="ospedale" id="ospedale" onchange="this.form.submit()" required>
            <option value="">Seleziona un ospedale</option>
            <?php
            while ($row = pg_fetch_assoc($result_ospedali)) {
                echo "<option value='{$row['codice']}'";
                if (isset($_POST['ospedale']) && $_POST['ospedale'] == $row['codice']) {
                    echo " selected";
                }
                echo ">{$row['nome']}</option>";
            }
            ?>
        </select><br>
        <?php if ($result_stanze && pg_num_rows($result_stanze) > 0) : ?>
        Stanza: 
        <select name="stanza" id="stanza" required>
            <option value="">Seleziona una stanza</option>
            <?php
            while ($row = pg_fetch_assoc($result_stanze)) {
                echo "<option value='{$row['id']}'>Numero: {$row['numero']}, Piano: {$row['piano']}</option>";
            }
            ?>
        </select><br>
        <input type="submit" name="submit" value="Aggiungi Ambulatorio Interno">
        <?php elseif ($result_stanze && pg_num_rows($result_stanze) === 0) : ?>
        <p>Nessuna stanza disponibile per l'ospedale selezionato.</p>
        <?php endif; ?>
    </form>

    <div class="back-button">
        <a href="visualizzaAmbulatoriInterni.php">Torna agli Ambulatori Interni</a>
    </div>
    
</body>
</html>

<?php
// Chiudi la connessione al database
pg_close($conn);
?>
