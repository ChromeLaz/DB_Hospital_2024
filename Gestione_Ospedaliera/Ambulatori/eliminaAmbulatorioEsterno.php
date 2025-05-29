<?php
// Include il file di configurazione per la connessione al database
include '../config.php';

// Variabile per verificare se l'eliminazione è riuscita
$deleteSuccess = false;

// Verifica se il parametro necessario è presente nell'URL
if (isset($_GET['codiceae'])) {
    $codiceae = $_GET['codiceae'];

    // Query per eliminare l'ambulatorio esterno
    $sql_elimina = "DELETE FROM ambulatorio_esterno WHERE codiceae = $1";
    $result_elimina = pg_query_params($conn, $sql_elimina, array($codiceae));

    if ($result_elimina) {
        $deleteSuccess = true;
    } else {
        echo "Errore durante l'eliminazione dell'ambulatorio: " . pg_last_error($conn);
    }
} else {
    echo "Parametro mancante per l'eliminazione.";
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elimina Ambulatorio Esterno</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f0f0f0;
        }
        .back-button {
            margin-top: 20px;
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
    <h2>Elimina Ambulatorio Esterno</h2>

    <?php
    // Mostra il messaggio di successo solo se l'eliminazione è avvenuta correttamente
    if ($deleteSuccess) {
        echo "<p>Ambulatorio Esterno eliminato con successo.</p>";
    }
    ?>

    <div class="back-button">
        <a href="visualizzaAmbulatoriEsterni.php">Torna agli Ambulatori Esterni</a>
    </div>
</body>
</html>

<?php
// Chiude la connessione al database
pg_close($conn);
?>
