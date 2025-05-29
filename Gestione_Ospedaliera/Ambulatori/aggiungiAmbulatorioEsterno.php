<?php
// Include il file di configurazione per la connessione al database
include '../config.php';

// Variabili per i dati del form e per gestire gli errori
$indirizzo = $telefono = $orario_apertura = '';
$indirizzoErr = $telefonoErr = $orario_aperturaErr = '';
$insertSuccess = false; // Variabile per verificare se l'inserimento è riuscito

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

    // Se non ci sono errori, procedi con l'inserimento nel database
    if (empty($indirizzoErr) && empty($telefonoErr) && empty($orario_aperturaErr)) {
        // Costruisce la query di inserimento per aggiungere un ambulatorio esterno
        $sql_inserimento = "INSERT INTO ambulatorio_esterno (indirizzo, telefono, orario_apertura) 
                            VALUES ($1, $2, $3)";
        $result_inserimento = pg_query_params($conn, $sql_inserimento, array($indirizzo, $telefono, $orario_apertura));

        if ($result_inserimento) {
            $insertSuccess = true;
        } else {
            echo "Errore durante l'inserimento dell'ambulatorio: " . pg_last_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aggiungi Ambulatorio Esterno</title>
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
    <h2>Aggiungi Ambulatorio Esterno</h2>
    <!-- Form per aggiungere un nuovo ambulatorio esterno -->
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        Indirizzo: <input type="text" name="indirizzo" value="<?php echo $indirizzo;?>" required>
        <span class="error"><?php echo $indirizzoErr;?></span><br>
        Telefono: <input type="text" name="telefono" value="<?php echo $telefono;?>" required>
        <span class="error"><?php echo $telefonoErr;?></span><br>
        Orario di Apertura: <input type="time" name="orario_apertura" value="<?php echo $orario_apertura;?>" required>
        <span class="error"><?php echo $orario_aperturaErr;?></span><br>
        <input type="submit" value="Aggiungi Ambulatorio Esterno">
    </form>

    <?php
    // Mostra il messaggio di successo solo se l'inserimento è avvenuto correttamente
    if ($insertSuccess) {
        echo "<p>Ambulatorio Esterno aggiunto con successo.</p>";
    }
    ?>

    <div class="back-button">
        <a href="visualizzaAmbulatoriEsterni.php">Torna agli Ambulatori Esterni</a>
    </div>
</body>
</html>

<?php
// Chiude la connessione al database
pg_close($conn);
?>
