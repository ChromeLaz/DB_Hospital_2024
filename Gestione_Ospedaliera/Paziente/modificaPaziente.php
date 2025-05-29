<?php
include '../config.php';

// Funzione per ottenere i dettagli di un paziente dal database
function getPaziente($cf) {
    $sql = "SELECT * FROM PAZIENTE WHERE cf = '$cf'";
    $result = pg_query($GLOBALS['conn'], $sql);
    return pg_fetch_assoc($result);
}

// Funzione per aggiornare i dettagli di un paziente nel database
function updatePaziente($cf, $nome, $cognome, $data_nascita) {
    $sql = "UPDATE PAZIENTE 
            SET nome = '$nome', cognome = '$cognome', data_nascita = '$data_nascita'
            WHERE cf = '$cf'";
    return pg_query($GLOBALS['conn'], $sql);
}

$paziente = null;
$success = null;

// Verifica se il metodo di richiesta è GET e se il parametro 'cf' è impostato
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['cf'])) {
    $paziente = getPaziente($_GET['cf']);
}

// Verifica se il metodo di richiesta è POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recupera i valori inviati dal modulo
    $cf = $_POST['cf'];
    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $data_nascita = $_POST['data_nascita'];
    // Aggiorna i dettagli del paziente nel database
    $success = updatePaziente($cf, $nome, $cognome, $data_nascita);
    $paziente = getPaziente($cf);
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Paziente</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }
        .container {
            width: 80%;
            margin: auto;
            margin-top: 50px;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="date"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        .btn {
            padding: 10px 20px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Modifica Paziente</h1>
        <?php if ($success !== null): ?>
            <div class="message <?php echo $success ? 'success' : 'error'; ?>">
                <?php echo $success ? 'Paziente aggiornato con successo!' : 'Errore nell\'aggiornamento del paziente.'; ?>
            </div>
        <?php endif; ?>
        <?php if ($paziente !== null): ?>
            <form method="post">
                <input type="hidden" name="cf" value="<?php echo $paziente['cf']; ?>">
                <div class="form-group">
                    <label for="nome">Nome:</label> <!-- Etichetta per il campo nome -->
                    <input type="text" id="nome" name="nome" value="<?php echo $paziente['nome']; ?>" required> <!-- Campo di input per il nome -->
                </div>
                <div class="form-group">
                    <label for="cognome">Cognome:</label> <!-- Etichetta per il campo cognome -->
                    <input type="text" id="cognome" name="cognome" value="<?php echo $paziente['cognome']; ?>" required> <!-- Campo di input per il cognome -->
                </div>
                <div class="form-group">
                    <label for="data_nascita">Data di Nascita:</label> <!-- Etichetta per il campo data di nascita -->
                    <input type="date" id="data_nascita" name="data_nascita" value="<?php echo $paziente['data_nascita']; ?>" required> <!-- Campo di input per la data di nascita -->
                </div>
                <button type="submit" class="btn">Aggiorna Paziente</button> <!-- Pulsante per inviare il modulo -->
            </form>
            <br>
            <br>

        <?php else: ?>
            <p>Paziente non trovato.</p> <!-- Messaggio di errore se il paziente non è stato trovato -->
        <?php endif; ?>
        <a href="paziente.php" class="btn" style="margin-top: 20px;">Torna Indietro</a> <!-- Link per tornare alla pagina principale -->
    </div>
</body>
</html>
