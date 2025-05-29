<?php
// Include il file di configurazione per la connessione al database
include '../config.php';

// Inizializza le variabili per l'ambulatorio e lo stato di aggiornamento
$ambulatorio = null;
$aggiornato = false;

// Controlla se il metodo della richiesta è POST e se 'id' è impostato
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id']; // Ottiene l'id dell'ambulatorio
    $stanza = $_POST['stanza']; // Ottiene la nuova stanza

    // Controlla se la stanza è già un ambulatorio interno nell'ospedale selezionato
    $sql_check_ambulatorio = "SELECT * FROM AMBULATORIO_INTERNO WHERE stanza='$stanza' AND codiceAI != '$id'";
    $result_check_ambulatorio = pg_query($conn, $sql_check_ambulatorio); // Esegue la query per controllare la stanza

    // Controlla se la stanza è una sala operatoria nell'ospedale selezionato
    $sql_check_sala_operatoria = "SELECT * FROM SALA_OPERATORIA WHERE stanza='$stanza'";
    $result_check_sala_operatoria = pg_query($conn, $sql_check_sala_operatoria);

    // Verifica se la stanza è già un ambulatorio interno o una sala operatoria
    if (pg_num_rows($result_check_ambulatorio) > 0) {
        echo "Errore: La stanza selezionata è già un ambulatorio interno.";
    } elseif (pg_num_rows($result_check_sala_operatoria) > 0) {
        echo "Errore: La stanza selezionata è una sala operatoria.";
    } else {
        // Costruisce la query di aggiornamento per modificare l'ambulatorio interno
        $sql_update = "UPDATE AMBULATORIO_INTERNO SET stanza='$stanza' WHERE codiceAI='$id'";

        // Esegue la query di aggiornamento e aggiorna $aggiornato a true se ha successo
        if (pg_query($conn, $sql_update)) {
            $aggiornato = true;
            $messaggio_successo = "Ambulatorio aggiornato con successo.";
        } else {
            // Mostra un messaggio di errore se l'aggiornamento non ha avuto successo
            echo "Errore: " . pg_last_error($conn);
        }
    }
} elseif (isset($_GET['id'])) {
    // Controlla se 'id' è stato inviato tramite GET
    $id = $_GET['id'];

    // Costruisce la query per ottenere i dettagli dell'ambulatorio interno
    $sql_select = "SELECT * FROM AMBULATORIO_INTERNO WHERE codiceAI='$id'";
    $result_select = pg_query($conn, $sql_select);

    // Verifica se l'ambulatorio è stato trovato
    if (pg_num_rows($result_select) > 0) {
        $ambulatorio = pg_fetch_assoc($result_select);
        
        // Query per ottenere le stanze disponibili nello stesso ospedale che non sono già ambulatori interni o sale operatorie
        $sql_stanze = "SELECT s.id, s.numero, s.piano, s.reparto_ospedale 
                       FROM STANZA s 
                       LEFT JOIN AMBULATORIO_INTERNO ai ON s.id = ai.stanza 
                       LEFT JOIN SALA_OPERATORIA so ON s.id = so.stanza 
                       WHERE s.reparto_ospedale = (SELECT reparto_ospedale FROM STANZA WHERE id = {$ambulatorio['stanza']}) 
                       AND ai.stanza IS NULL AND so.stanza IS NULL 
                       AND s.id != {$ambulatorio['stanza']}";
        $result_stanze = pg_query($conn, $sql_stanze); // Esegue la query e memorizza il risultato
    } else {
        echo "Ambulatorio non trovato.";
        exit;
    }
} else {
    echo "ID ambulatorio non fornito.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Ambulatorio Interno</title>
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
    <h2>Modifica Ambulatorio Interno</h2>
    <?php if ($aggiornato && isset($messaggio_successo)) : ?>
        <!-- Mostra un messaggio di successo se l'ambulatorio è stato aggiornato -->
        <p><?php echo htmlspecialchars($messaggio_successo); ?></p>
        <div class="back-button">
            <a href="visualizzaAmbulatoriInterni.php">Torna agli Ambulatori Interni</a>
        </div>
    <?php elseif ($ambulatorio) : ?>
        <!-- Mostra il form di modifica se l'ambulatorio è stato trovato -->
        <form method="post" action="modificaAmbulatorioInterno.php">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($ambulatorio['codiceai']); ?>">
            Ospedale: 
            <select name="ospedale" id="ospedale" disabled>
                <?php
                // Query per ottenere i dettagli dell'ospedale corrente
                $sql_ospedale_corrente = "SELECT o.codice, o.nome FROM OSPEDALE o
                                          JOIN STANZA s ON o.codice = s.reparto_ospedale
                                          WHERE s.id = {$ambulatorio['stanza']}";
                $result_ospedale_corrente = pg_query($conn, $sql_ospedale_corrente);
                if ($row = pg_fetch_assoc($result_ospedale_corrente)) {
                    echo "<option value='{$row['codice']}' selected>{$row['nome']}</option>";
                }
                ?>
            </select><br>
            Stanza: 
            <select name="stanza" id="stanza" required>
                <?php
                // Popola il select delle stanze disponibili nello stesso ospedale
                while ($row = pg_fetch_assoc($result_stanze)) {
                    $selected = ($row['id'] == $ambulatorio['stanza']) ? 'selected' : '';
                    echo "<option value='{$row['id']}' data-ospedale='{$row['reparto_ospedale']}' $selected>Numero: {$row['numero']}, Piano: {$row['piano']}</option>";
                }
                ?>
            </select><br>
            <input type="submit" value="Modifica Ambulatorio Interno">
        </form>
    <?php else : ?>
        <!-- Mostra un messaggio di errore se l'ambulatorio non è stato trovato -->
        <p>Ambulatorio non trovato.</p>
    <?php endif; ?>
    <div class="back-button">
        <a href="visualizzaAmbulatoriInterni.php">Torna agli Ambulatori Interni</a>
    </div>
</body>
</html>

<?php
// Chiude la connessione al database
pg_close($conn);
?>
