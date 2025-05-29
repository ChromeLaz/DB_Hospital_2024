<?php
include '../config.php';

// Funzione per eliminare un paziente dal database
function deletePaziente($cf) {
    $sql = "DELETE FROM PAZIENTE WHERE cf = '$cf'";
    return pg_query($GLOBALS['conn'], $sql);
}

$success = null;

// Verifica se il metodo di richiesta è GET e se il parametro 'cf' è impostato
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['cf'])) {
    // Elimina il paziente con il codice fiscale specificato
    $success = deletePaziente($_GET['cf']);
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elimina Paziente</title>
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
            text-align: center;
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
        .btn {
            padding: 10px 20px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Elimina Paziente</h1>
        <?php if ($success !== null): ?>
            <div class="message <?php echo $success ? 'success' : 'error'; ?>">
                <?php echo $success ? 'Paziente eliminato con successo!' : 'Errore nell\'eliminazione del paziente.'; ?>
            </div>
        <?php else: ?>
            <p>Paziente non trovato.</p> <!-- Messaggio di default se il paziente non è stato trovato -->
        <?php endif; ?>
        <a href="paziente.php" class="btn" style="margin-top: 20px;">Torna Indietro</a> <!-- Link per tornare alla pagina principale -->
    </div>
</body>
</html>
