<?php
include '../config.php'; // Includiamo il file di configurazione del database per connetterci

$successMessage = ""; // Messaggio di successo inizialmente vuoto
$errorMessage = ""; // Messaggio di errore inizialmente vuoto
 
// Controlliamo se la richiesta HTTP è di tipo POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Acquisiamo i dati inviati dal modulo
    $nome = $_POST['nome'];
    $indirizzo = $_POST['indirizzo'];
    $citta = $_POST['citta'];
    $ha_pronto_soccorso = isset($_POST['ha_pronto_soccorso']) ? 1 : 0; // Controlliamo se la casella del pronto soccorso è selezionata
    $nome_pronto_soccorso = $_POST['nome_pronto_soccorso'];

    // Verifichiamo se è stato selezionato "Ha Pronto Soccorso" ma il nome non è stato fornito
    if ($ha_pronto_soccorso && empty($nome_pronto_soccorso)) {
        $errorMessage = "Se selezioni 'Ha Pronto Soccorso', devi fornire un nome per il Pronto Soccorso.";
    } else {
        // Prepariamo la query SQL per inserire un nuovo ospedale nella tabella OSPEDALE
        $query = "INSERT INTO OSPEDALE (nome, indirizzo, città) VALUES ($1, $2, $3)";
        // Eseguiamo la query utilizzando pg_query_params per evitare SQL injection
        $result = pg_query_params($conn, $query, array($nome, $indirizzo, $citta));

        // Verifichiamo se l'inserimento dell'ospedale è andato a buon fine
        if ($result) {
            // Otteniamo l'ID dell'ospedale appena inserito
            $ospedale_id_query = "SELECT codice FROM OSPEDALE WHERE nome=$1 AND indirizzo=$2 AND città=$3 ORDER BY codice DESC LIMIT 1";
            $ospedale_id_result = pg_query_params($conn, $ospedale_id_query, array($nome, $indirizzo, $citta));
            if ($ospedale_id_result && pg_num_rows($ospedale_id_result) > 0) {
                $ospedale_id = pg_fetch_result($ospedale_id_result, 0, 'codice');

                // Se è stato selezionato il pronto soccorso, inseriamo i relativi dati nella tabella PS
                if ($ha_pronto_soccorso) {
                    $query_prontosoccorso = "INSERT INTO PRONTOSOCCORSO (nome, ospedale) VALUES ($1, $2)";
                    pg_query_params($conn, $query_prontosoccorso, array($nome_pronto_soccorso, $ospedale_id));
                }
                $successMessage = "Ospedale aggiunto con successo"; // Impostiamo il messaggio di successo
            } else {
                $errorMessage = "Errore durante l'acquisizione dell'ID dell'ospedale.";
            }
        } else {
            // In caso di errore, impostiamo il messaggio di errore con il dettaglio dell'errore
            $errorMessage = "Errore durante l'aggiunta dell'ospedale: " . pg_last_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aggiungi Ospedale</title>
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
        input[type="text"], input[type="submit"], input[type="checkbox"] {
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
        <h2>Aggiungi Ospedale</h2>
        <form method="post" action="aggiungiOspedale.php">
            Nome: <input type="text" name="nome" required><br> <!-- Campo per il nome dell'ospedale -->
            Città: <input type="text" name="citta" required><br> <!-- Campo per la città -->
            Indirizzo: <input type="text" name="indirizzo" required><br> <!-- Campo per l'indirizzo -->
            Ha Pronto Soccorso: <input type="checkbox" name="ha_pronto_soccorso" id="ha_pronto_soccorso" onclick="toggleProntoSoccorso()"><br> <!-- Checkbox per il pronto soccorso -->
            Nome Pronto Soccorso: <input type="text" name="nome_pronto_soccorso" id="nome_pronto_soccorso"><br> <!-- Campo per il nome del pronto soccorso -->
            <input type="submit" value="Aggiungi"> <!-- Pulsante per inviare il modulo -->
        </form>
        <?php if ($successMessage) : ?>
            <div class="message"><?php echo $successMessage; ?></div> <!-- Mostra il messaggio di successo -->
        <?php endif; ?>
        <?php if ($errorMessage) : ?>
            <div class="error"><?php echo $errorMessage; ?></div> <!-- Mostra il messaggio di errore -->
        <?php endif; ?>
        <div class="back-button">
            <a href="ospedale.php">Torna indietro</a>
        </div>
    </div>
    <script>
        function toggleProntoSoccorso() {
            const checkbox = document.getElementById('ha_pronto_soccorso');
            const nomeProntoSoccorso = document.getElementById('nome_pronto_soccorso');

            if (checkbox.checked) {
                nomeProntoSoccorso.required = true; // Rende il campo obbligatorio se la casella è selezionata
            } else {
                nomeProntoSoccorso.required = false; // Rende il campo non obbligatorio se la casella non è selezionata
            }
        }
    </script>
</body>
</html>
