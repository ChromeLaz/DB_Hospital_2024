<?php
include '../config.php'; // Includiamo il file di configurazione del database per connetterci

$successMessage = ""; // Messaggio di successo inizialmente vuoto
$errorMessage = ""; // Messaggio di errore inizialmente vuoto
$ospedale = null; // Variabile per memorizzare i dati dell'ospedale

// Controlliamo se la richiesta HTTP è di tipo POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Acquisiamo i dati inviati dal modulo
    $codice = $_POST['codice'];
    $nome = $_POST['nome'];
    $indirizzo = $_POST['indirizzo'];
    $citta = $_POST['citta'];
    $ha_pronto_soccorso = isset($_POST['ha_pronto_soccorso']) ? 1 : 0; // Controlliamo se la casella del pronto soccorso è selezionata
    $nome_pronto_soccorso = $_POST['nome_pronto_soccorso'];

    // Prepariamo la query SQL per aggiornare i dati dell'ospedale nella tabella OSPEDALE
    $query = "UPDATE OSPEDALE SET nome = $1, indirizzo = $2, città = $3 WHERE codice = $4";
    // Eseguiamo la query utilizzando pg_query_params per evitare SQL injection
    $result = pg_query_params($conn, $query, array($nome, $indirizzo, $citta, $codice));

    // Verifichiamo se l'aggiornamento dell'ospedale è andato a buon fine
    if ($result) {
        // Se è stato selezionato il pronto soccorso, aggiorniamo o inseriamo i relativi dati nella tabella PS
        if ($ha_pronto_soccorso && !empty($nome_pronto_soccorso)) {
            // Controlliamo se esiste già un record per il pronto soccorso di questo ospedale
            $query_ps_check = "SELECT * FROM PRONTOSOCCORSO  WHERE ospedale = $1";
            $result_ps_check = pg_query_params($conn, $query_ps_check, array($codice));

            if (pg_num_rows($result_ps_check) > 0) {
                // Aggiorniamo il record esistente
                $query_ps_update = "UPDATE PRONTOSOCCORSO  SET nome = $1 WHERE ospedale = $2";
                pg_query_params($conn, $query_ps_update, array($nome_pronto_soccorso, $codice));
            } else {
                // Inseriamo un nuovo record
                $query_ps_insert = "INSERT INTO PRONTOSOCCORSO  (nome, ospedale) VALUES ($1, $2)";
                pg_query_params($conn, $query_ps_insert, array($nome_pronto_soccorso, $codice));
            }
        } else {
            // Se il pronto soccorso non è selezionato, eliminiamo eventuali record esistenti nella tabella PS
            $query_ps_delete = "DELETE FROM PRONTOSOCCORSO  WHERE ospedale = $1";
            pg_query_params($conn, $query_ps_delete, array($codice));
        }
        $successMessage = "Ospedale aggiornato con successo"; // Impostiamo il messaggio di successo
    } else {
        // In caso di errore, impostiamo il messaggio di errore con il dettaglio dell'errore
        $errorMessage = "Errore durante l'aggiornamento dell'ospedale: " . pg_last_error($conn);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['codice'])) {
    $codice = $_GET['codice']; // Acquisiamo il codice dell'ospedale dalla richiesta GET

    // Prepariamo la query SQL per ottenere i dati dell'ospedale
    $query = "SELECT OSPEDALE.codice, OSPEDALE.nome, OSPEDALE.città, OSPEDALE.indirizzo, PRONTOSOCCORSO.nome AS nome_pronto_soccorso
              FROM OSPEDALE
              LEFT JOIN prontosoccorso  ON OSPEDALE.codice = PRONTOSOCCORSO.ospedale
              WHERE OSPEDALE.codice = $1";
    // Eseguiamo la query utilizzando pg_query_params per evitare SQL injection
    $result = pg_query_params($conn, $query, array($codice));

    // Verifichiamo se abbiamo ottenuto dei risultati
    if ($result && pg_num_rows($result) > 0) {
        $ospedale = pg_fetch_assoc($result); // Memorizziamo i dati dell'ospedale
    } else {
        $errorMessage = "Ospedale non trovato."; // Impostiamo il messaggio di errore se l'ospedale non è trovato
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Ospedale</title>
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
        <h2>Modifica Ospedale</h2>
        <?php if ($ospedale): ?>
        <form method="post" action="modificaOspedale.php">
            Codice: <input type="text" name="codice" value="<?php echo $ospedale['codice']; ?>" readonly><br> <!-- Campo codice non modificabile -->
            Nome: <input type="text" name="nome" value="<?php echo $ospedale['nome']; ?>" required><br> <!-- Campo nome -->
            Città: <input type="text" name="citta" value="<?php echo $ospedale['città']; ?>" required><br> <!-- Campo città -->
            Indirizzo: <input type="text" name="indirizzo" value="<?php echo $ospedale['indirizzo']; ?>" required><br> <!-- Campo indirizzo -->
            Ha Pronto Soccorso: <input type="checkbox" name="ha_pronto_soccorso" <?php echo $ospedale['nome_pronto_soccorso'] ? 'checked' : ''; ?>><br> <!-- Checkbox pronto soccorso -->
            Nome Pronto Soccorso: <input type="text" name="nome_pronto_soccorso" value="<?php echo $ospedale['nome_pronto_soccorso']; ?>"><br> <!-- Campo nome pronto soccorso -->
            <input type="submit" value="Modifica"> <!-- Pulsante per inviare il modulo -->
        </form>
        <?php endif; ?>
        <?php if ($successMessage) : ?>
            <div class="message"><?php echo $successMessage; ?></div> <!-- Mostra il messaggio di successo -->
        <?php endif; ?>
        <?php if ($errorMessage) : ?>
            <div class="error"><?php echo $errorMessage; ?></div> <!-- Mostra il messaggio di errore -->
        <?php endif; ?>
        <div class="back-button">
            <a href="ospedale.php">Torna alla lista degli ospedali</a> 
        </div>
    </div>
</body>
</html>
