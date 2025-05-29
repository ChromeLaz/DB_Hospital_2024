<?php
// Include il file di configurazione per la connessione al database
include '../config.php';

// Inizializza le variabili per l'ambulatorio e lo stato di eliminazione
$ambulatorio = null;
$eliminato = false;

// Controlla se il metodo della richiesta è POST e se 'id' è impostato
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id']; // Ottiene l'id dell'ambulatorio

    // Costruisce la query per eliminare l'ambulatorio interno
    $sql_delete = "DELETE FROM AMBULATORIO_INTERNO WHERE codiceAI='$id'";

    // Esegue la query di eliminazione e aggiorna $eliminato a true se ha successo
    if (pg_query($conn, $sql_delete)) {
        $eliminato = true;
    } else {
        // Mostra un messaggio di errore se l'eliminazione non ha avuto successo
        echo "Errore: " . pg_last_error($conn);
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
    <title>Elimina Ambulatorio Interno</title>
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
        form input[type="submit"] {
            background-color: #FF6347;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            padding: 10px;
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
    <h2>Elimina Ambulatorio Interno</h2>
    <?php if ($eliminato): ?>
        <!-- Mostra un messaggio di successo se l'ambulatorio è stato eliminato -->
        <p>Ambulatorio eliminato con successo.</p>
        <div class="back-button">
            <a href="visualizzaAmbulatoriInterni.php">Torna agli Ambulatori Interni</a>
        </div>
    <?php elseif ($ambulatorio): ?>
        <!-- Mostra il form di eliminazione se l'ambulatorio è stato trovato -->
        <form method="post" action="eliminaAmbulatorioInterno.php">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($ambulatorio['codiceai']); ?>">
            <p>Sei sicuro di voler eliminare l'ambulatorio interno relativo alla stanza <?php echo htmlspecialchars($ambulatorio['stanza']); ?>?</p>
            <input type="submit" value="Elimina Ambulatorio Interno">
        </form>
    <?php else: ?>
        <!-- Mostra un messaggio di errore se l'ambulatorio non è stato trovato -->
        <p>Ambulatorio non trovato.</p>
        <div class="back-button">
            <a href="visualizzaAmbulatoriInterni.php">Torna agli Ambulatori Interni</a>
        </div>
    <?php endif; ?>
</body>
</html>

<?php
// Chiude la connessione al database
pg_close($conn);
?>
