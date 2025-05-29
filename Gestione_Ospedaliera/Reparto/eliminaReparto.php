<?php
// Include il file di configurazione che contiene la connessione al database
include '../config.php';

// Variabili per i messaggi di successo e di errore
$successMessage = "";
$errorMessage = "";

// Controlla se il modulo di eliminazione è stato inviato
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_reparto'])) {
    // Recupera i valori inviati dal modulo
    $nome = $_POST['nome'];
    $ospedale = $_POST['ospedale'];

    // Query SQL per eliminare il reparto specifico
    $query = "DELETE FROM REPARTO WHERE nome = $1 AND ospedale = $2";
    // Esegue la query con i parametri forniti per prevenire SQL injection
    $result = pg_query_params($conn, $query, array($nome, $ospedale));

    // Verifica se la query ha avuto successo
    if ($result) {
        $successMessage = "Reparto eliminato con successo";
    } else {
        $errorMessage = "Errore durante l'eliminazione del reparto: " . pg_last_error($conn);
    }
}

// Recupera la lista degli ospedali per il dropdown
$ospedaliQuery = "SELECT codice, nome FROM OSPEDALE";
$ospedaliResult = pg_query($conn, $ospedaliQuery);

// Controlla se è stato selezionato un ospedale
$ospedale_id = null;
if (isset($_POST['select_ospedale'])) {
    $ospedale_id = $_POST['ospedale_id'];
    // Query SQL per recuperare i reparti dell'ospedale selezionato
    $repartiQuery = "SELECT nome FROM REPARTO WHERE ospedale = $1";
    // Esegue la query con il parametro fornito per prevenire SQL injection
    $repartiResult = pg_query_params($conn, $repartiQuery, array($ospedale_id));
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elimina Reparto</title>
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
        input[type="text"], select, input[type="submit"] {
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
        <h1>Elimina Reparto</h1>

        <!-- Modulo per selezionare un ospedale -->
        <?php if (!isset($ospedale_id)): ?>
            <form method="POST" action="">
                <label for="ospedale_id">Seleziona un Ospedale:</label><br>
                <select id="ospedale_id" name="ospedale_id" required>
                    <option value="">--Seleziona un Ospedale--</option>
                    <!-- Ciclo attraverso gli ospedali e li aggiunge al dropdown -->
                    <?php while ($row = pg_fetch_assoc($ospedaliResult)): ?>
                        <option value="<?php echo $row['codice']; ?>"><?php echo $row['nome']; ?></option>
                    <?php endwhile; ?>
                </select><br>
                <input type="submit" name="select_ospedale" value="Seleziona">
            </form>
        <?php endif; ?>

        <!-- Modulo per selezionare e eliminare un reparto, mostrato solo se un ospedale è stato selezionato -->
        <?php if (isset($repartiResult)): ?>
            <form method="POST" action="">
                <label for="reparto">Seleziona un Reparto:</label><br>
                <select id="reparto" name="nome" required>
                    <option value="">--Seleziona un Reparto--</option>
                    <!-- Ciclo attraverso i reparti e li aggiunge al dropdown -->
                    <?php while ($row = pg_fetch_assoc($repartiResult)): ?>
                        <option value="<?php echo $row['nome']; ?>"><?php echo $row['nome']; ?></option>
                    <?php endwhile; ?>
                </select><br>
                <!-- Campo nascosto per passare l'ID dell'ospedale al modulo di eliminazione -->
                <input type="hidden" name="ospedale" value="<?php echo $ospedale_id; ?>">
                <input type="submit" name="delete_reparto" value="Elimina">
            </form>
        <?php endif; ?>
        <!-- Messaggi di successo o errore -->
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
