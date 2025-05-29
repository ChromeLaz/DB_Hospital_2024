<?php
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ospedale_id = $_POST['ospedale_id'];

    $query = "SELECT * FROM REPARTO WHERE ospedale = $1";
    $result = pg_query_params($conn, $query, array($ospedale_id));
}

// Recupera gli ospedali per il dropdown
$ospedaliQuery = "SELECT codice, nome FROM OSPEDALE";
$ospedaliResult = pg_query($conn, $ospedaliQuery);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizza Reparti</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f0f0f0;
        }
        .container {
            margin-top: 50px;
        }
        table {
            width: 80%;
            border-collapse: collapse;
            margin: 0 auto;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .message {
            margin-top: 50px;
            color: #333;
            font-size: 1.2em;
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
        select, input[type="submit"] {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            box-sizing: border-box;
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
    <!-- Inizia il contenitore principale della pagina -->
    <div class="container">
        <!-- Intestazione della pagina -->
        <h1>Visualizza Reparti</h1>
        
        <!-- Form per selezionare un ospedale -->
        <form method="POST" action="">
            <!-- Etichetta per il campo di selezione dell'ospedale -->
            <label for="ospedale_id">Seleziona un Ospedale:</label><br>
            
            <!-- Dropdown per selezionare un ospedale -->
            <select id="ospedale_id" name="ospedale_id" required>
                <!-- Opzione predefinita -->
                <option value="">--Seleziona un Ospedale--</option>
                
                <!-- Loop PHP per popolare il dropdown con gli ospedali dal database -->
                <?php while ($row = pg_fetch_assoc($ospedaliResult)): ?>
                    <!-- Opzione per ogni ospedale -->
                    <option value="<?php echo $row['codice']; ?>" <?php echo isset($ospedale_id) && $ospedale_id == $row['codice'] ? 'selected' : ''; ?>>
                        <?php echo $row['nome']; ?>
                    </option>
                <?php endwhile; ?>
            </select><br>
            
            <!-- Pulsante per inviare il form -->
            <input type="submit" value="Visualizza Reparti">
        </form>
        
        <!-- Controlla se ci sono risultati da mostrare -->
        <?php if (isset($result) && pg_num_rows($result) > 0): ?>
            <!-- Tabella per visualizzare i reparti -->
            <table>
                <!-- Intestazione della tabella -->
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Telefono</th>
                        <th>Orario di Visita</th>
                    </tr>
                </thead>
                <!-- Corpo della tabella -->
                <tbody>
                    <!-- Loop PHP per popolare la tabella con i reparti -->
                    <?php while ($row = pg_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $row['nome']; ?></td>
                            <td><?php echo $row['telefono']; ?></td>
                            <td><?php echo $row['orario_visita']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <!-- Messaggio se non ci sono reparti trovati -->
        <?php elseif (isset($result)): ?>
            <div class="message">Nessun reparto trovato per questo ospedale.</div>
        <?php endif; ?>
        
        <!-- Pulsante per tornare alla pagina principale -->
        <div class="back-button">
            <a href="reparto.php">Torna alla pagina principale</a>
        </div>
    </div>
    <!-- Fine del contenitore principale -->
</body>
</html>

