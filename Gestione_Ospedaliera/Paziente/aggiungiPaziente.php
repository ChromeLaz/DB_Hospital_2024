<?php
include '../config.php';

// Funzione per aggiungere un paziente al database
function addPaziente($cf, $nome, $cognome, $data_nascita) {
    $sql = "INSERT INTO PAZIENTE (cf, nome, cognome, data_nascita) 
            VALUES ('$cf', '$nome', '$cognome', '$data_nascita')";
    return pg_query($GLOBALS['conn'], $sql);
}

$success = null;
// Verifica se il metodo di richiesta è POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recupera i valori dal modulo
    $cf = $_POST['cf'];
    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $data_nascita = $_POST['data_nascita'];
    // Aggiunge il paziente al database
    $success = addPaziente($cf, $nome, $cognome, $data_nascita);
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aggiungi Paziente</title>
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
        <h1>Aggiungi Paziente</h1>
        <?php if ($success !== null): ?>
            <div class="message <?php echo $success ? 'success' : 'error'; ?>">
                <?php echo $success ? 'Paziente aggiunto con successo!' : 'Errore nell\'aggiunta del paziente.'; ?>
            </div>
        <?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label for="cf">Codice Fiscale:</label> <!-- Etichetta per il codice fiscale -->
                <input type="text" id="cf" name="cf" required> <!-- Campo di input per il codice fiscale -->
            </div>
            <div class="form-group">
                <label for="nome">Nome:</label> <!-- Etichetta per il nome -->
                <input type="text" id="nome" name="nome" required> <!-- Campo di input per il nome -->
            </div>
            <div class="form-group">
                <label for="cognome">Cognome:</label> <!-- Etichetta per il cognome -->
                <input type="text" id="cognome" name="cognome" required> <!-- Campo di input per il cognome -->
            </div>
            <div class="form-group">
                <label for="data_nascita">Data di Nascita:</label> <!-- Etichetta per la data di nascita -->
                <input type="date" id="data_nascita" name="data_nascita" required> <!-- Campo di input per la data di nascita -->
            </div>
            <button type="submit" class="btn">Aggiungi Paziente</button> <!-- Pulsante per inviare il modulo -->
        </form>
        <br>
        <br>

        <a href="paziente.php" class="btn" style="margin-top: 20px;">Torna Indietro</a> <!-- Link per tornare alla pagina principale -->
    </div>
</body>
</html>
