<?php
// Include il file di configurazione che contiene la connessione al database
include '../config.php';

// Variabili per i messaggi di successo e di errore
$successMessage = "";
$errorMessage = "";

// Verifica se il modulo è stato inviato con il metodo POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recupera i valori inviati dal modulo, con validazione base
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $ospedale = isset($_POST['ospedale']) ? trim($_POST['ospedale']) : '';
    $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
    $orario_di_visita = isset($_POST['orario_di_visita']) ? trim($_POST['orario_di_visita']) : '';

    // Verifica che tutti i campi richiesti siano stati forniti
    if (empty($nome) || empty($ospedale) || empty($telefono) || empty($orario_di_visita)) {
        $errorMessage = "Tutti i campi sono obbligatori. Si prega di compilare tutti i campi.";
    } elseif (!preg_match('/^\d{2}:\d{2}-\d{2}:\d{2}$/', $orario_di_visita)) {
        $errorMessage = "L'orario di visita non è nel formato corretto (HH:MM-HH:MM).";
    } elseif (!preg_match('/^\d{3}-\d{7}$/', $telefono)) {
        $errorMessage = "Il numero di telefono non è nel formato corretto (es. 055-1234567).";
    } else {
        // Query SQL per inserire un nuovo reparto
        $query = "INSERT INTO REPARTO (nome, ospedale, telefono, orario_visita) VALUES ($1, $2, $3, $4)";
        // Esegue la query con i parametri forniti per prevenire SQL injection
        $result = pg_query_params($conn, $query, array($nome, $ospedale, $telefono, $orario_di_visita));

        // Verifica se la query ha avuto successo
        if ($result) {
            $successMessage = "Reparto aggiunto con successo";
        } else {
            $errorMessage = "Errore durante l'aggiunta del reparto: " . pg_last_error($conn);
        }
    }
}

// Recupera la lista degli ospedali per il dropdown
$ospedaliQuery = "SELECT codice, nome FROM OSPEDALE";
$ospedaliResult = pg_query($conn, $ospedaliQuery);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserisci Reparto</title>
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
        input[type="text"], input[type="time"], select, input[type="submit"] {
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
        <h1>Inserisci Reparto</h1>
        <!-- Modulo per l'inserimento di un nuovo reparto -->
        <form method="POST" action="">
            <label for="nome">Nome:</label><br>
            <input type="text" id="nome" name="nome" required><br>
            <label for="ospedale">Ospedale:</label><br>
            <select id="ospedale" name="ospedale" required>
                <option value="">--Seleziona un Ospedale--</option>
                <!-- Ciclo attraverso gli ospedali e li aggiunge al dropdown -->
                <?php while ($row = pg_fetch_assoc($ospedaliResult)): ?>
                    <option value="<?php echo $row['codice']; ?>"><?php echo $row['nome']; ?></option>
                <?php endwhile; ?>
            </select><br>
            <label for="telefono">Telefono:</label><br>
            <input type="text" id="telefono" name="telefono" pattern="^\d{3}-\d{7}$" placeholder="055-1234567" required><br>
            <label for="orario_di_visita">Orario di Visita (HH:MM-HH:MM):</label><br>
            <input type="text" id="orario_di_visita" name="orario_di_visita" pattern="^\d{2}:\d{2}-\d{2}:\d{2}$" placeholder="09:00-17:00" required><br>
            <input type="submit" value="Inserisci">
        </form>
        <!-- Mostra i messaggi di successo o errore -->
        <?php if ($successMessage): ?>
            <div class="message"><?php echo $successMessage; ?></div>
        <?php endif; ?>
        <?php if ($errorMessage): ?>
            <div class="error"><?php echo $errorMessage; ?></div>
        <?php endif; ?>
        <!-- Pulsante per tornare alla pagina principale -->
        <div class="back-button">
            <a href="reparto.php">Torna alla pagina principale</a>
        </div>
    </div>
</body>
</html>
