<?php

//Includere il file di configurazione del database
include '../config.php';

// Funzione per ottenere tutte le prenotazioni
function getPrenotazioni() {
    // Definire la query SQL per selezionare tutte le prenotazioni con i dettagli del paziente e dell'esame
    $sql = "SELECT PE.*, P.nome, P.cognome, E.descrizione, E.costopr, E.costopu 
            FROM PRENOTAZIONE_ESAME PE
            JOIN PAZIENTE P ON PE.paziente = P.cf
            JOIN ESAME E ON PE.esame = E.codice";
    // Eseguire la query e verificare se ci sono errori
    $result = pg_query($GLOBALS['conn'], $sql);
    if (!$result) {
        error_log("Error fetching prenotazioni: " . pg_last_error($GLOBALS['conn']));
        return [];
    }
    // Restituire i risultati della query
    return pg_fetch_all($result);
}

// Ottenere tutte le prenotazioni
$prenotazioni = getPrenotazioni();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizza Prenotazioni</title>
    <style>
        /* Stile per il corpo della pagina */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }
        /* Stile per il contenitore principale */
        .container {
            width: 90%;
            margin: auto;
            margin-top: 50px;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        /* Stile per le tabelle */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        /* Stile per le celle delle tabelle */
        table, th, td {
            border: 1px solid #ddd;
        }
        /* Stile per le celle di intestazione delle tabelle */
        th, td {
            padding: 8px;
            text-align: left;
        }
        /* Stile per le celle di intestazione delle tabelle */
        th {
            background-color: #f2f2f2;
        }
        /* Stile per i pulsanti */
        .btn {
            padding: 10px 20px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        /* Stile per i pulsanti al passaggio del mouse */
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Visualizza Prenotazioni</h1>
        <!-- Tabella per visualizzare le prenotazioni -->
        <table>
            <thead>
                <tr>
                    <th>Codice Prenotazione</th>
                    <th>Data di Prenotazione</th>
                    <th>Regime</th>
                    <th>Urgenza</th>
                    <th>Paziente</th>
                    <th>Esame</th>
                    <th>Data Esame</th>
                    <th>Ora Esame</th>
                    <th>Medico Prescrittore</th>
                    <th>Avvertenze</th>
                    <th>Costo</th>
                </tr>
            </thead>
            <tbody>
                <!-- Verifica se ci sono prenotazioni da visualizzare -->
                <?php if ($prenotazioni): ?>
                    <!-- Ciclo per ogni prenotazione -->
                    <?php foreach ($prenotazioni as $prenotazione): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($prenotazione['codice_prenotazione']); ?></td>
                            <td><?php echo htmlspecialchars($prenotazione['data_di_prenotazione']); ?></td>
                            <td><?php echo htmlspecialchars($prenotazione['regime']); ?></td>
                            <td><?php echo htmlspecialchars($prenotazione['urgenza']); ?></td>
                            <td><?php echo htmlspecialchars($prenotazione['nome'] . ' ' . $prenotazione['cognome']); ?></td>
                            <td><?php echo htmlspecialchars($prenotazione['descrizione']); ?></td>
                            <td><?php echo htmlspecialchars($prenotazione['data']); ?></td>
                            <td><?php echo htmlspecialchars($prenotazione['ora']); ?></td>
                            <td><?php echo $prenotazione['medico_prescrittore'] ? htmlspecialchars($prenotazione['medico_prescrittore']) : 'N/A'; ?></td>
                            <td><?php echo $prenotazione['avvertenze'] ? htmlspecialchars($prenotazione['avvertenze']) : 'N/A'; ?></td>
                            <td>
                                <!-- Calcolare il costo in base al regime -->
                                <?php 
                                    if ($prenotazione['regime'] == 'Pubblico') {
                                        echo isset($prenotazione['costopu']) ? htmlspecialchars($prenotazione['costopu']) . ' €' : 'N/A';
                                    } else {
                                        echo isset($prenotazione['costopr']) ? htmlspecialchars($prenotazione['costopr']) . ' €' : 'N/A';
                                    }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="11">Nessuna prenotazione trovata.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <br>
        <br>
        <a href="prenotazione.php" class="btn" style="margin-top: 20px;">Torna Indietro</a>
    </div>
</body>
</html>
