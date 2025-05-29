<?php
// Includi il file di configurazione che contiene la connessione al database
include '../config.php';

// Variabili per i messaggi di successo e di errore
$successMessage = "";
$errorMessage = "";

// Verifica se il metodo di richiesta è POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recupera il valore dell'ID dell'esame dal form
    $codice = $_POST['esame_id'];

    // Query SQL per eliminare l'esame con il codice specificato
    $query = "DELETE FROM ESAME WHERE codice = $1";
    // Esegui la query con il parametro codice
    $result = pg_query_params($conn, $query, array($codice));

    // Verifica se la query è stata eseguita con successo
    if ($result) {
        // Se sì, imposta il messaggio di successo
        $successMessage = "Esame eliminato con successo";
    } else {
        // Se no, imposta il messaggio di errore con il dettaglio dell'errore
        $errorMessage = "Errore durante l'eliminazione dell'esame: " . pg_last_error($conn);
    }
}

// Query SQL per recuperare gli esami per il dropdown
$esamiQuery = "SELECT codice, descrizione FROM ESAME";
// Esegui la query
$esamiResult = pg_query($conn, $esamiQuery);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elimina Esame</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f0f0f0;
        }
        .container {
            margin-top: 50px;
        }
        form {
            margin: 20px;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: inline-block;
            text-align: left;
        }
        select, input[type="submit"] {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            box-sizing: border-box;
        }
        .message {
            margin-top: 20px;
            color: green;
        }
        .error {
            margin-top: 20px;
            color: red;
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
    <div class="container">
        <h1>Elimina Esame</h1>
        <!-- Form per la selezione e l'eliminazione dell'esame -->
        <form method="POST" action="">
            <label for="esame_id">Seleziona un Esame:</label><br> <!-- Etichetta per il menu a tendina degli esami -->
            <select id="esame_id" name="esame_id" required>
                <option value="">--Seleziona un Esame--</option> <!-- Opzione predefinita del menu a tendina -->
                <!-- Ciclo per popolare il dropdown con gli esami disponibili -->
                <?php while ($row = pg_fetch_assoc($esamiResult)): ?> 
                    <option value="<?php echo $row['codice']; ?>"><?php echo $row['descrizione']; ?></option> <!-- Opzioni del menu a tendina -->
                <?php endwhile; ?>
            </select><br>
            <input type="submit" value="Elimina"> <!-- Pulsante per inviare il form -->
        </form>
        <!-- Messaggio di successo -->
        <?php if ($successMessage): ?> 
            <div class="message"><?php echo $successMessage; ?></div> <!-- Div per il messaggio di successo -->
        <?php endif; ?>
        <!-- Messaggio di errore -->
        <?php if ($errorMessage): ?> 
            <div class="error"><?php echo $errorMessage; ?></div> <!-- Div per il messaggio di errore -->
        <?php endif; ?>
        <!-- Pulsante per tornare alla pagina principale -->
        <div class="back-button">
            <a href="esame.php">Torna alla pagina principale</a> <!-- Link per tornare alla pagina principale -->
        </div>
    </div>
</body>
</html>
