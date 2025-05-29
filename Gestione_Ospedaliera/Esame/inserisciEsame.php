<?php
// Includi il file di configurazione che contiene la connessione al database
include '../config.php';

// Variabili per i messaggi di successo e di errore
$successMessage = "";
$errorMessage = "";

// Verifica se il metodo di richiesta è POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recupera i valori dal form
    $descrizione = $_POST['descrizione'];
    $costoPR = $_POST['costoPR'];
    $costoPU = $_POST['costoPU'];

    // Query SQL per inserire un nuovo esame
    $query = "INSERT INTO ESAME (descrizione, costoPR, costoPU) VALUES ($1, $2, $3)";
    // Esegui la query con i parametri forniti
    $result = pg_query_params($conn, $query, array($descrizione, $costoPR, $costoPU));

    // Verifica se la query è stata eseguita con successo
    if ($result) {
        // Se sì, imposta il messaggio di successo
        $successMessage = "Esame aggiunto con successo";
    } else {
        // Se no, imposta il messaggio di errore con il dettaglio dell'errore
        $errorMessage = "Errore durante l'aggiunta dell'esame: " . pg_last_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserisci Esame</title>
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
        input[type="text"], input[type="number"], input[type="submit"] {
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
        <h1>Inserisci Esame</h1>
        <!-- Form per inserire un nuovo esame -->
        <form method="POST" action="">
            <label for="descrizione">Descrizione:</label><br> <!-- Etichetta per la descrizione -->
            <input type="text" id="descrizione" name="descrizione" required><br> <!-- Campo di input per la descrizione -->
            <label for="costoPR">Costo PR:</label><br> <!-- Etichetta per il costo PR -->
            <input type="number" id="costoPR" name="costoPR" step="0.01" required><br> <!-- Campo di input per il costo PR -->
            <label for="costoPU">Costo PU:</label><br> <!-- Etichetta per il costo PU -->
            <input type="number" id="costoPU" name="costoPU" step="0.01" required><br> <!-- Campo di input per il costo PU -->
            <input type="submit" value="Inserisci"> <!-- Pulsante per inviare il form -->
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
