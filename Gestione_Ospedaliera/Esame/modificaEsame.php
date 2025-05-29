<?php
// Inizia una sessione
session_start();
// Include il file di configurazione che contiene la connessione al database
include '../config.php';

// Variabili di stato per controllare se un esame è stato selezionato e per i messaggi di successo o errore
$esameSelezionato = false;
$successMessage = '';
$errorMessage = '';

// Se il modulo di selezione è stato inviato
if (isset($_POST['select_exam'])) {
    // Recupera il codice dell'esame selezionato
    $codiceEsame = $_POST['codice_esame'];
    // Query per recuperare i dettagli dell'esame selezionato
    $query = "SELECT * FROM ESAME WHERE codice = $1";
    // Esegue la query con il parametro fornito
    $result = pg_query_params($conn, $query, array($codiceEsame));
    // Se la query ha successo
    if ($result) {
        // Recupera i dati dell'esame
        $esame = pg_fetch_assoc($result);
        $esameSelezionato = true;
    } else {
        // Imposta il messaggio di errore
        $errorMessage = "Errore nel recupero dei dati dell'esame.";
    }
}

// Se il modulo di aggiornamento è stato inviato
if (isset($_POST['update_exam'])) {
    // Recupera i valori dal form di aggiornamento
    $codiceEsame = $_POST['codice_esame'];
    $descrizione = $_POST['descrizione'];
    $costopr = $_POST['costopr'];
    $costopu = $_POST['costopu'];

    // Query per aggiornare i dati dell'esame
    $query = "UPDATE ESAME SET descrizione = $1, costopr = $2, costopu = $3 WHERE codice = $4";
    // Esegue la query con i parametri forniti
    $result = pg_query_params($conn, $query, array($descrizione, $costopr, $costopu, $codiceEsame));
    // Se la query ha successo
    if ($result) {
        // Imposta il messaggio di successo e resetta lo stato di selezione dell'esame
        $successMessage = "Esame aggiornato con successo.";
        $esameSelezionato = false;
    } else {
        // Imposta il messaggio di errore
        $errorMessage = "Errore nell'aggiornamento dei dati dell'esame.";
    }
}

// Recupera la lista degli esami per il menu a tendina
$query = "SELECT codice, descrizione FROM ESAME ORDER BY codice ASC";
$esamiResult = pg_query($conn, $query);

// Chiude la connessione al database
pg_close($conn);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Esame</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f0f0f0;
            padding: 20px;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: inline-block;
        }
        h1 {
            margin-bottom: 20px;
        }
        select, input[type="text"], input[type="number"] {
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: calc(100% - 22px);
        }
        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            margin-bottom: 20px;
            font-weight: bold;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Modifica Esame</h1>
        <?php
        // Mostra i messaggi di successo o errore
        if (!empty($successMessage)) {
            echo "<p class='message success'>$successMessage</p>";
        }
        if (!empty($errorMessage)) {
            echo "<p class='message error'>$errorMessage</p>";
        }
        ?>
        <?php if (!$esameSelezionato) { ?>
            <!-- Form per la selezione di un esame da modificare -->
            <form method="post" action="modificaEsame.php">
                <label for="codice_esame">Seleziona un esame da modificare:</label> <!-- Etichetta per il menu a tendina degli esami -->
                <select id="codice_esame" name="codice_esame" required>
                    <option value="">Seleziona esame</option> <!-- Opzione predefinita del menu a tendina -->
                    <?php while ($row = pg_fetch_assoc($esamiResult)) { ?>
                        <option value="<?php echo $row['codice']; ?>"><?php echo $row['descrizione']; ?> (Codice: <?php echo $row['codice']; ?>)</option> <!-- Opzioni del menu a tendina -->
                    <?php } ?>
                </select>
                <button type="submit" name="select_exam">Seleziona</button> <!-- Pulsante per inviare il form di selezione -->
            </form>
        <?php } else { ?>
            <!-- Form per l'aggiornamento dei dettagli dell'esame selezionato -->
            <form method="post" action="modificaEsame.php">
                <input type="hidden" name="codice_esame" value="<?php echo $esame['codice']; ?>"> <!-- Campo nascosto per il codice dell'esame -->
                <label for="descrizione">Descrizione:</label> <!-- Etichetta per la descrizione -->
                <input type="text" id="descrizione" name="descrizione" value="<?php echo $esame['descrizione']; ?>" required> <!-- Campo di input per la descrizione -->
                <label for="costopr">Costo PR:</label> <!-- Etichetta per il costo PR -->
                <input type="number" id="costopr" name="costopr" step="0.01" value="<?php echo $esame['costopr']; ?>" required> <!-- Campo di input per il costo PR -->
                <label for="costopu">Costo PU:</label> <!-- Etichetta per il costo PU -->
                <input type="number" id="costopu" name="costopu" step="0.01" value="<?php echo $esame['costopu']; ?>" required> <!-- Campo di input per il costo PU -->
                <button type="submit" name="update_exam">Aggiorna</button> <!-- Pulsante per inviare il form di aggiornamento -->
            </form>
        <?php } ?>
        <br>
        <a href="esame.php" class="back-button">Torna Indietro</a> <!-- Link per tornare alla pagina principale -->
    </div>
</body>
</html>
