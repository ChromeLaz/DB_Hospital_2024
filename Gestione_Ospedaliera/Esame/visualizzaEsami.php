<?php
// Inizia una sessione per mantenere lo stato tra le pagine
session_start();
// Include il file di configurazione che contiene le informazioni di connessione al database
include '../config.php';

// Variabile per memorizzare i messaggi da visualizzare all'utente
$message = "";

// Verifica se il modulo è stato inviato con il metodo POST e il campo 'codice' è impostato
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['codice'])) {
    // Recupera i valori inviati dal modulo
    $codice = $_POST['codice'];
    $descrizione = $_POST['descrizione'];
    $costopr = $_POST['costopr'];
    $costopu = $_POST['costopu'];

    // Query SQL per aggiornare i dettagli dell'esame con i valori forniti
    $query = "UPDATE ESAME SET descrizione = $1, costopr = $2, costopu = $3 WHERE codice = $4";
    // Esegue la query con i parametri forniti per prevenire SQL injection
    $result = pg_query_params($conn, $query, array($descrizione, $costopr, $costopu, $codice));

    // Verifica se l'aggiornamento è andato a buon fine
    if ($result) {
        $message = "Esame aggiornato con successo.";
    } else {
        $message = "Si è verificato un errore durante l'aggiornamento dell'esame.";
    }
}

// Query SQL per recuperare la lista degli esami da visualizzare nella tabella
$query = "SELECT codice, descrizione, costopr, costopu FROM ESAME ORDER BY codice ASC";
$result = pg_query($conn, $query);

// Controlla se la query per recuperare gli esami ha avuto successo
if (!$result) {
    $error_message = "Si è verificato un errore durante il recupero degli esami.";
}

// Chiude la connessione al database
pg_close($conn);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Esami</title>
    <style>
        /* Stile di base per il corpo della pagina */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            text-align: center;
            padding: 20px;
        }
        /* Contenitore per il modulo e la tabella */
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            margin-bottom: 20px;
        }
        h1 {
            margin-bottom: 20px;
        }
        /* Stile della tabella */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
        }
        th {
            background-color: #f4f4f4;
        }
        /* Messaggi di successo o errore */
        .message {
            text-align: center;
            margin-bottom: 10px;
            font-weight: bold;
        }
        /* Pulsanti di azione */
        .back-button, .edit-button {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            background-color: #007bff;
            color: #fff;
            border-radius: 4px;
            text-decoration: none;
        }
        .back-button:hover, .edit-button:hover {
            background-color: #0056b3;
        }
        /* Modulo di modifica, inizialmente nascosto */
        .edit-form {
            display: none;
            flex-direction: column;
            align-items: center;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        /* Stile per gli input del modulo */
        input[type="text"], input[type="number"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        /* Pulsante di invio del modulo */
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
    <script>
        // Funzione per mostrare il modulo di modifica con i dati dell'esame selezionato
        function showEditForm(codice, descrizione, costopr, costopu) {
            // Imposta il display del modulo di modifica su 'flex'
            document.getElementById('edit-form').style.display = 'flex';
            // Assegna i valori ai campi del modulo di modifica
            document.getElementById('codice').value = codice;
            document.getElementById('descrizione').value = descrizione;
            document.getElementById('costopr').value = costopr;
            document.getElementById('costopu').value = costopu;
        }
    </script>
</head>
<body>
    <h1>Modifica Esami</h1>
    <?php
    // Mostra il messaggio di successo o errore, se presente
    if (isset($message)) {
        echo "<p class='message'>$message</p>";
    }
    if (isset($error_message)) {
        echo "<p class='message'>$error_message</p>";
    } else {
        // Mostra la tabella degli esami
        echo "<div class='container'>";
        echo "<table>";
        echo "<tr><th>Codice</th><th>Descrizione</th><th>Costo PR</th><th>Costo PU</th><th>Azione</th></tr>";
        // Cicla attraverso i risultati della query e crea una riga per ciascun esame
        while ($row = pg_fetch_assoc($result)) {
            // Gestisce valori null per i campi costo PR e costo PU
            $costopr = isset($row['costopr']) ? $row['costopr'] : 'N/A';
            $costopu = isset($row['costopu']) ? $row['costopu'] : 'N/A';
            echo "<tr>";
            echo "<td>{$row['codice']}</td>";
            echo "<td>{$row['descrizione']}</td>";
            echo "<td>{$costopr}</td>";
            echo "<td>{$costopu}</td>";
            // Pulsante per modificare l'esame, con chiamata alla funzione showEditForm
            echo "<td><button class='edit-button' onclick=\"showEditForm('{$row['codice']}', '{$row['descrizione']}', '{$costopr}', '{$costopu}')\">Modifica</button></td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";
    }
    ?>
    <!-- Modulo di modifica esame nascosto inizialmente -->
    <div class="container edit-form" id="edit-form">
        <h2>Modifica Esame</h2>
        <form method="POST" action="">
            <!-- Campo nascosto per il codice dell'esame -->
            <input type="hidden" id="codice" name="codice">
            <label for="descrizione">Descrizione:</label> <!-- Etichetta per la descrizione dell'esame -->
            <!-- Campo di testo per la descrizione dell'esame -->
            <input type="text" id="descrizione" name="descrizione" required>
            <label for="costopr">Costo PR:</label> <!-- Etichetta per il costo PR -->
            <!-- Campo di input numerico per il costo PR -->
            <input type="number" step="0.01" id="costopr" name="costopr" required>
            <label for="costopu">Costo PU:</label> <!-- Etichetta per il costo PU -->
            <!-- Campo di input numerico per il costo PU -->
            <input type="number" step="0.01" id="costopu" name="costopu" required>
            <!-- Pulsante per inviare il modulo di aggiornamento -->
            <input type="submit" value="Aggiorna Esame">
        </form>
    </div>
    <!-- Pulsante per tornare indietro alla pagina principale -->
    <a href="esame.php" class="back-button">Torna Indietro</a>
</body>
</html>
