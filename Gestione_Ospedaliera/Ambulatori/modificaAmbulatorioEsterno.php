<?php
// Include il file di configurazione per la connessione al database
include '../config.php';

// Variabili per i dati del form e per gestire gli errori
$indirizzo = $telefono = $orario_apertura = '';
$indirizzoErr = $telefonoErr = $orario_aperturaErr = '';
$updateSuccess = false; // Variabile per verificare se l'aggiornamento è riuscito
$ambulatorio = null;

// Funzione per mostrare il messaggio di successo
function mostraMessaggioSuccesso() {
    echo "<p>Ambulatorio Esterno modificato con successo.</p>";
    echo '<div class="back-button"><a href="visualizzaAmbulatoriEsterni.php">Torna agli Ambulatori Esterni</a></div>';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validazione dei dati
    if (empty($_POST['indirizzo'])) {
        $indirizzoErr = "Indirizzo obbligatorio";
    } else {
        $indirizzo = $_POST['indirizzo'];
    }
    
    if (empty($_POST['telefono'])) {
        $telefonoErr = "Telefono obbligatorio";
    } else {
        $telefono = $_POST['telefono'];
    }
    
    if (empty($_POST['orario_apertura'])) {
        $orario_aperturaErr = "Orario di apertura obbligatorio";
    } else {
        $orario_apertura = $_POST['orario_apertura'];
    }

    // Se non ci sono errori, procedi con l'aggiornamento nel database
    if (empty($indirizzoErr) && empty($telefonoErr) && empty($orario_aperturaErr)) {
        // Aggiorna l'ambulatorio esterno
        $sql_aggiorna = "UPDATE ambulatorio_esterno SET indirizzo = $1, telefono = $2, orario_apertura = $3 WHERE codiceae = $4";
        $result_aggiorna = pg_query_params($conn, $sql_aggiorna, array($indirizzo, $telefono, $orario_apertura, $_POST['codiceae']));

        if ($result_aggiorna) {
            $updateSuccess = true;
            mostraMessaggioSuccesso();
            pg_close($conn);
            exit;
        } else {
            echo "Errore durante l'aggiornamento dell'ambulatorio: " . pg_last_error($conn);
        }
    }
}

// Verifica se il parametro codiceae è presente nell'URL
if (isset($_GET['codiceae'])) {
    $codiceae = $_GET['codiceae'];

    // Query per ottenere i dati dell'ambulatorio esterno da modificare
    $sql_ambulatorio = "SELECT * FROM ambulatorio_esterno WHERE codiceae = $1";
    $result_ambulatorio = pg_query_params($conn, $sql_ambulatorio, array($codiceae));
    if ($result_ambulatorio && pg_num_rows($result_ambulatorio) > 0) {
        $ambulatorio = pg_fetch_assoc($result_ambulatorio);
    } else {
        echo "Ambulatorio non trovato.";
        pg_close($conn);
        exit;
    }
} else {
    echo "Parametro codiceae mancante.";
    pg_close($conn);
    exit;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Ambulatorio Esterno</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f0f0f0;
        }
        form {
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: left;
        }
        form input {
            width: 100%;
            padding: 8px;
            margin: 8px 0;
            box-sizing: border-box;
        }
        form input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        form input[type="submit"]:hover {
            background-color: #45a049;
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
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <h2>Modifica Ambulatorio Esterno</h2>
    <!-- Form per modificare un ambulatorio esterno -->
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <input type="hidden" name="codiceae" value="<?php echo htmlspecialchars($ambulatorio['codiceae']); ?>">
        Indirizzo: <input type="text" name="indirizzo" value="<?php echo htmlspecialchars($ambulatorio['indirizzo']);?>" required>
        <span class="error"><?php echo $indirizzoErr;?></span><br>
        Telefono: <input type="text" name="telefono" value="<?php echo htmlspecialchars($ambulatorio['telefono']);?>" required>
        <span class="error"><?php echo $telefonoErr;?></span><br>
        Orario di Apertura: <input type="time" name="orario_apertura" value="<?php echo htmlspecialchars($ambulatorio['orario_apertura']);?>" required>
        <span class="error"><?php echo $orario_aperturaErr;?></span><br>
        <input type="submit" value="Modifica Ambulatorio Esterno">
    </form>
</body>
</html>

<?php
// Chiude la connessione al database
pg_close($conn);
?>
