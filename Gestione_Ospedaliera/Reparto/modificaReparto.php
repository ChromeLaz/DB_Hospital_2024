<?php
include '../config.php';

// Funzione per ottenere tutti gli ospedali
function getOspedali($conn) {
    $sql = "SELECT codice, nome FROM OSPEDALE";
    return pg_query($conn, $sql);
}

// Funzione per ottenere i reparti di un ospedale
function getReparti($conn, $ospedale_id) {
    $sql = "SELECT nome FROM REPARTO WHERE ospedale = $1";
    return pg_query_params($conn, $sql, array($ospedale_id));
}

$successMessage = "";

// Se il form di selezione ospedale è stato inviato
if (isset($_POST['select_ospedale'])) {
    $ospedale_id = $_POST['ospedale_id'];
    $repartiResult = getReparti($conn, $ospedale_id);
}

// Se il form di selezione reparto è stato inviato
if (isset($_POST['select_reparto'])) {
    $ospedale_id = $_POST['ospedale_id'];
    $reparto_nome = $_POST['reparto_nome'];
    $query = "SELECT * FROM REPARTO WHERE nome = $1 AND ospedale = $2";
    $result = pg_query_params($conn, $query, array($reparto_nome, $ospedale_id));
    $reparto = pg_fetch_assoc($result);
}

// Se il form di modifica è stato inviato
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['modifica'])) {
    $nome = $_POST['nome'];
    $ospedale = $_POST['ospedale'];
    $telefono = $_POST['telefono'];
    $orario_visita_inizio = $_POST['orario_visita_inizio'];
    $orario_visita_fine = $_POST['orario_visita_fine'];

    // Formatta l'orario di visita in un formato corretto (esempio: "HH:MM-HH:MM")
    $orario_visita = date("H:i", strtotime($orario_visita_inizio)) . "-" . date("H:i", strtotime($orario_visita_fine));

    $query = "UPDATE REPARTO SET telefono = $1, orario_visita = $2 WHERE nome = $3 AND ospedale = $4";
    $result = pg_query_params($conn, $query, array($telefono, $orario_visita, $nome, $ospedale));

    if (!$result) {
        echo "Errore durante l'aggiornamento: " . pg_last_error($conn);
        exit();
    }

    $successMessage = "Modifica avvenuta con successo";
    // Aggiorna i dati del reparto per riflettere le modifiche nel modulo
    $reparto = ['nome' => $nome, 'ospedale' => $ospedale, 'telefono' => $telefono, 'orario_visita' => $orario_visita];
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Reparto</title>
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
        select, input[type="text"], input[type="time"], input[type="submit"] {
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
        <h1>Modifica Reparto</h1>

        <?php if (!isset($reparto) && !isset($repartiResult)): ?>
            <!-- Form per selezionare un ospedale -->
            <form method="POST" action="">
                <label for="ospedale_id">Seleziona un Ospedale:</label><br>
                <select id="ospedale_id" name="ospedale_id" required>
                    <option value="">--Seleziona un Ospedale--</option>
                    <?php
                    $ospedali = getOspedali($conn);
                    while ($row = pg_fetch_assoc($ospedali)) {
                        echo "<option value='{$row['codice']}'" . (isset($ospedale_id) && $ospedale_id == $row['codice'] ? " selected" : "") . ">{$row['nome']}</option>";
                    }
                    ?>
                </select><br>
                <input type="submit" name="select_ospedale" value="Seleziona">
            </form>
        <?php elseif (!isset($reparto) && isset($repartiResult)): ?>
            <!-- Form per selezionare un reparto -->
            <form method="POST" action="">
                <input type="hidden" name="ospedale_id" value="<?php echo $ospedale_id; ?>">
                <label for="reparto_nome">Seleziona un Reparto:</label><br>
                <select id="reparto_nome" name="reparto_nome" required>
                    <option value="">--Seleziona un Reparto--</option>
                    <?php while ($row = pg_fetch_assoc($repartiResult)): ?>
                        <option value="<?php echo $row['nome']; ?>"<?php echo isset($reparto_nome) && $reparto_nome == $row['nome'] ? " selected" : ""; ?>><?php echo $row['nome']; ?></option>
                    <?php endwhile; ?>
                </select><br>
                <input type="submit" name="select_reparto" value="Seleziona">
            </form>
        <?php else: ?>
            <!-- Form per modificare il reparto selezionato -->
            <form method="POST" action="">
                <input type="hidden" name="ospedale" value="<?php echo $reparto['ospedale']; ?>">
                <input type="hidden" name="nome" value="<?php echo $reparto['nome']; ?>">
                <label for="telefono">Telefono:</label><br>
                <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($reparto['telefono']); ?>" required><br>
                <label for="orario_visita_inizio">Orario di Visita Inizio:</label><br>
                <input type="time" id="orario_visita_inizio" name="orario_visita_inizio" value="<?php echo substr($reparto['orario_visita'], 0, 5); ?>" required><br>
                <label for="orario_visita_fine">Orario di Visita Fine:</label><br>
                <input type="time" id="orario_visita_fine" name="orario_visita_fine" value="<?php echo substr($reparto['orario_visita'], 6, 5); ?>" required><br>
                <input type="submit" name="modifica" value="Modifica">
            </form>
            <?php if ($successMessage): ?>
                <p><?php echo $successMessage; ?></p>
            <?php endif; ?>
        <?php endif; ?>

        <div class="back-button">
            <a href="modificaReparto.php">Seleziona un altro Reparto</a> |
            <a href="reparto.php">Torna alla pagina principale</a>
        </div>
    </div>
</body>
</html>
