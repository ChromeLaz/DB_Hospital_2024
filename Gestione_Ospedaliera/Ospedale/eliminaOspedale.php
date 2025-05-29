<?php
include '../config.php'; // Includiamo il file di configurazione del database per connetterci

$successMessage = ""; // Messaggio di successo inizialmente vuoto
$errorMessage = ""; // Messaggio di errore inizialmente vuoto

// Controlliamo se la richiesta HTTP è di tipo POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Acquisiamo il codice dell'ospedale dal form
    $codice = $_POST['codice'];

    // Prepariamo la query SQL per eliminare l'ospedale dalla tabella OSPEDALE
    $query = "DELETE FROM OSPEDALE WHERE codice = $1";
    // Eseguiamo la query utilizzando pg_query_params per evitare SQL injection
    $result = pg_query_params($conn, $query, array($codice));

    // Verifichiamo se l'eliminazione dell'ospedale è andata a buon fine
    if ($result) {
        $successMessage = "Ospedale eliminato con successo"; // Impostiamo il messaggio di successo
    } else {
        // In caso di errore, impostiamo il messaggio di errore con il dettaglio dell'errore
        $errorMessage = "Errore durante l'eliminazione dell'ospedale: " . pg_last_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elimina Ospedale</title>
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
        input[type="text"], input[type="submit"] {
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
        <h2>Elimina Ospedale</h2>
        <!-- Form per eliminare un ospedale specificando il codice -->
        <form method="post" action="eliminaOspedale.php">
            Codice: <input type="text" name="codice" required><br> <!-- Campo codice ospedale -->
            <input type="submit" value="Elimina"> <!-- Pulsante per inviare il modulo -->
        </form>
        <?php if ($successMessage) : ?>
            <div class="message"><?php echo $successMessage; ?></div> <!-- Mostra il messaggio di successo -->
        <?php endif; ?>
        <?php if ($errorMessage) : ?>
            <div class="error"><?php echo $errorMessage; ?></div> <!-- Mostra il messaggio di errore -->
        <?php endif; ?>
        <div class="back-button">
            <a href="ospedale.php">Torna indietro
        </div>
    </div>
</body>
</html>
