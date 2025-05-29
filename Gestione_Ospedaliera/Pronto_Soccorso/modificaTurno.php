<?php
// Include il file di configurazione per la connessione al database
include '../config.php';

$turno = null; // Inizializza la variabile $turno come null
$aggiornato = false; // Inizializza la variabile $aggiornato come false

// Controlla se il metodo della richiesta è POST e se unique_id è stato inviato
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['unique_id'])) {
    // Ottiene i dati inviati dal modulo
    $data = $_POST['data'];
    $orario_inizio = $_POST['orario_inizio'];
    $orario_fine = $_POST['orario_fine'];
    $cf = $_POST['cf'];
    $PRONTOSOCCORSO= $_POST['PRONTOSOCCORSO'];
    $ospedale = $_POST['ospedale'];
    $tipo = $_POST['tipo'];
    $unique_id = $_POST['unique_id'];

    // Decodifica l'ID univoco per ottenere i dettagli originali del turno
    $original_turno = json_decode(base64_decode($unique_id), true);

    // Costruisce la query di aggiornamento in base al tipo di personale
    if ($tipo == 'infermiere') {
        $sql_update = "UPDATE TURNO_INFERMIERE SET data='$data', orario_inizio='$orario_inizio', orario_fine='$orario_fine'
                       WHERE data='{$original_turno['data']}' AND orario_inizio='{$original_turno['orario_inizio']}' AND orario_fine='{$original_turno['orario_fine']}' AND cf='{$original_turno['cf']}' AND PRONTOSOCCORSO='{$original_turno['PRONTOSOCCORSO']}' AND ospedale='{$original_turno['ospedale']}'";
    } else {
        $sql_update = "UPDATE TURNO_MEDICO SET data='$data', orario_inizio='$orario_inizio', orario_fine='$orario_fine'
                       WHERE data='{$original_turno['data']}' AND orario_inizio='{$original_turno['orario_inizio']}' AND orario_fine='{$original_turno['orario_fine']}' AND cf='{$original_turno['cf']}' AND PRONTOSOCCORSO='{$original_turno['PRONTOSOCCORSO']}' AND ospedale='{$original_turno['ospedale']}'";
    }

    // Esegue la query di aggiornamento e aggiorna $aggiornato a true se ha successo
    if (pg_query($conn, $sql_update)) {
        $aggiornato = true;
    } else {
        // Mostra un messaggio di errore se l'aggiornamento non ha avuto successo
        echo "Errore: " . pg_last_error($conn);
    }
} elseif (isset($_GET['id']) && isset($_GET['tipo'])) {
    // Controlla se id e tipo sono stati inviati tramite GET
    $unique_id = $_GET['id']; // Ottiene l'ID univoco del turno
    $tipo = $_GET['tipo']; // Ottiene il tipo di personale

    // Decodifica l'ID univoco per ottenere i dettagli del turno
    $turno = json_decode(base64_decode($unique_id), true);

    // Mostra un messaggio di errore se il turno non è stato trovato
    if (!$turno) {
        echo "Turno non trovato.";
        exit;
    }
} else {
    // Mostra un messaggio di errore se ID o tipo non sono stati forniti
    echo "ID turno o tipo non forniti.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Turno</title>
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
</head>
<body>
    <h2>Modifica Turno</h2>
    <?php if ($aggiornato): ?>
        <!-- Mostra un messaggio di successo se il turno è stato aggiornato -->
        <p>Turno aggiornato con successo.</p>
        <div class="back-button">
            <a href="visualizzaTurni.php">Torna ai Turni</a>
        </div>
    <?php elseif ($turno): ?>
        <!-- Mostra il form di modifica se il turno è stato trovato -->
        <form method="post" action="modificaTurno.php">
            <input type="hidden" name="unique_id" value="<?php echo htmlspecialchars($unique_id); ?>">
            <input type="hidden" name="tipo" value="<?php echo htmlspecialchars($tipo); ?>">
            PRONTOSOCCORSO: <input type="text" name="PRONTOSOCCORSO" value="<?php echo htmlspecialchars($turno['PRONTOSOCCORSO']); ?>" readonly><br>
            Codice Fiscale: <input type="text" name="cf" value="<?php echo htmlspecialchars($turno['cf']); ?>" readonly><br>
            Ospedale: <input type="text" name="ospedale" value="<?php echo htmlspecialchars($turno['ospedale']); ?>" readonly><br>
            Data: <input type="date" name="data" value="<?php echo htmlspecialchars($turno['data']); ?>" required><br>
            Orario di Inizio: <input type="time" name="orario_inizio" value="<?php echo htmlspecialchars($turno['orario_inizio']); ?>" required><br>
            Orario di Fine: <input type="time" name="orario_fine" value="<?php echo htmlspecialchars($turno['orario_fine']); ?>" required><br>
            <input type="submit" value="Modifica Turno">
        </form>
    <?php else: ?>
        <!-- Mostra un messaggio di errore se il turno non è stato trovato -->
        <p>Turno non trovato.</p>
        <div class="back-button">
            <a href="visualizzaTurni.php">Torna ai Turni</a>
        </div>
    <?php endif; ?>
</body>
</html>

<?php
// Chiude la connessione al database
pg_close($conn);
?>
