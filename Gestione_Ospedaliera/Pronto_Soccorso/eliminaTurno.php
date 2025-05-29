<?php
// Include il file di configurazione per la connessione al database
include '../config.php';

$turno = null; // Inizializza la variabile $turno come null
$eliminato = false; // Inizializza la variabile $eliminato come false

// Controlla se il metodo della richiesta è POST e se unique_id è stato inviato
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['unique_id'])) {
    $unique_id = $_POST['unique_id']; // Ottiene l'ID univoco del turno
    $tipo = $_POST['tipo']; // Ottiene il tipo di personale (infermiere o medico)

    // Decodifica l'ID univoco per ottenere i dettagli originali del turno
    $original_turno = json_decode(base64_decode($unique_id), true);

    // Crea la query di eliminazione in base al tipo di personale
    if ($tipo == 'infermiere') {
        $sql_delete = "DELETE FROM TURNO_INFERMIERE 
                       WHERE data='{$original_turno['data']}' AND orario_inizio='{$original_turno['orario_inizio']}' AND orario_fine='{$original_turno['orario_fine']}' AND cf='{$original_turno['cf']}' AND PRONTOSOCCORSO='{$original_turno['PRONTOSOCCORSO']}' AND ospedale='{$original_turno['ospedale']}'";
    } else {
        $sql_delete = "DELETE FROM TURNO_MEDICO 
                       WHERE data='{$original_turno['data']}' AND orario_inizio='{$original_turno['orario_inizio']}' AND orario_fine='{$original_turno['orario_fine']}' AND cf='{$original_turno['cf']}' AND PRONTOSOCCORSO='{$original_turno['PRONTOSOCCORSO']}' AND ospedale='{$original_turno['ospedale']}'";
    }

    // Esegue la query di eliminazione e controlla se ha avuto successo
    if (pg_query($conn, $sql_delete)) {
        $eliminato = true; // Imposta $eliminato a true se l'eliminazione ha avuto successo
    } else {
        // Mostra un messaggio di errore se l'eliminazione non ha avuto successo
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
    <title>Elimina Turno</title>
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
            background-color: #FF6347;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        form input[type="submit"]:hover {
            background-color: #FF4500;
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
    <h2>Elimina Turno</h2>
    <?php if ($eliminato): ?>
        <!-- Mostra un messaggio di successo se il turno è stato eliminato -->
        <p>Turno eliminato con successo.</p>
        <div class="back-button">
            <a href="visualizzaTurni.php">Torna ai Turni</a>
        </div>
    <?php elseif ($turno): ?>
        <!-- Mostra un modulo di conferma se il turno non è stato ancora eliminato -->
        <p>Sei sicuro di voler eliminare il seguente turno?</p>
        <form method="post" action="eliminaTurno.php">
            <input type="hidden" name="unique_id" value="<?php echo htmlspecialchars($unique_id); ?>">
            <input type="hidden" name="tipo" value="<?php echo htmlspecialchars($tipo); ?>">
            PRONTOSOCCORSO: <input type="text" name="PRONTOSOCCORSO" value="<?php echo htmlspecialchars($turno['PRONTOSOCCORSO']); ?>" readonly><br>
            Codice Fiscale: <input type="text" name="cf" value="<?php echo htmlspecialchars($turno['cf']); ?>" readonly><br>
            Ospedale: <input type="text" name="ospedale" value="<?php echo htmlspecialchars($turno['ospedale']); ?>" readonly><br>
            Data: <input type="date" name="data" value="<?php echo htmlspecialchars($turno['data']); ?>" readonly><br>
            Orario di Inizio: <input type="time" name="orario_inizio" value="<?php echo htmlspecialchars($turno['orario_inizio']); ?>" readonly><br>
            Orario di Fine: <input type="time" name="orario_fine" value="<?php echo htmlspecialchars($turno['orario_fine']); ?>" readonly><br>
            <input type="submit" value="Elimina Turno">
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
