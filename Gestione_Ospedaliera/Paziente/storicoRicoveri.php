<?php
include '../config.php';

// Funzione per ottenere la lista dei pazienti dal database
function getPazienti() {
    $sql = "SELECT cf, nome, cognome FROM PAZIENTE"; // Query per selezionare tutti i pazienti
    $result = pg_query($GLOBALS['conn'], $sql); // Esegui la query
    if (!$result) {
        echo "Errore nella query: " . pg_last_error($GLOBALS['conn']);
        return [];
    }
    return pg_fetch_all($result); // Restituisci tutti i risultati come array associativo
}

// Funzione per ottenere la lista dei ricoveri di un paziente specifico dal database
function getRicoveri($cf) {
    $sql = "SELECT R.data_inizio, R.data_fine, S.numero AS numero_stanza, S.piano, S.reparto_nome, O.nome AS nome_ospedale, P.nome AS patologia
            FROM RICOVERO R
            JOIN LETTO L ON R.letto = L.numero AND R.stanza = L.stanza
            JOIN STANZA S ON L.stanza = S.id
            JOIN REPARTO RIC ON S.reparto_nome = RIC.nome AND S.reparto_ospedale = RIC.ospedale
            JOIN OSPEDALE O ON RIC.ospedale = O.codice
            JOIN PRESENTA PZ ON R.paziente = PZ.paziente
            JOIN PATOLOGIA P ON PZ.patologia = P.nome
            WHERE R.paziente = '$cf'"; // Query per selezionare i ricoveri di un paziente specifico
    $result = pg_query($GLOBALS['conn'], $sql); // Esegui la query
    if (!$result) {
        echo "Errore nella query: " . pg_last_error($GLOBALS['conn']);
        return [];
    }
    return pg_fetch_all($result); // Restituisci tutti i risultati come array associativo
}

$pazienti = getPazienti(); // Ottieni la lista dei pazienti
$ricoveri = [];
if (isset($_GET['cf'])) {
    $ricoveri = getRicoveri($_GET['cf']); // Ottieni i ricoveri del paziente selezionato
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Storico Ricoveri Paziente</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        .btn-back {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }
        .btn-back:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Storico Ricoveri Paziente</h1> <!-- Titolo della pagina -->
        
        <div class="form-group">
            <label for="pazienti">Seleziona Paziente:</label> <!-- Etichetta per il menu a tendina dei pazienti -->
            <select id="pazienti" name="pazienti" onchange="location = this.value;"> <!-- Menu a tendina per selezionare un paziente -->
                <option value="">Seleziona un paziente</option> <!-- Opzione di default -->
                <?php
                // Popola il menu a tendina con la lista dei pazienti
                foreach ($pazienti as $paziente) {
                    echo "<option value='?cf={$paziente['cf']}'";
                    if (isset($_GET['cf']) && $_GET['cf'] == $paziente['cf']) {
                        echo " selected";
                    }
                    echo ">{$paziente['nome']} {$paziente['cognome']}</option>";
                }
                ?>
            </select>
        </div>

        <?php if (!empty($ricoveri)): ?>
            <h2>Ricoveri per <?php echo $_GET['cf']; ?></h2> <!-- Sottotitolo con il codice fiscale del paziente selezionato -->
            <table>
                <tr>
                    <th>Data Inizio</th> <!-- Intestazione della colonna Data Inizio -->
                    <th>Data Fine</th> <!-- Intestazione della colonna Data Fine -->
                    <th>Numero Stanza</th> <!-- Intestazione della colonna Numero Stanza -->
                    <th>Piano</th> <!-- Intestazione della colonna Piano -->
                    <th>Reparto</th> <!-- Intestazione della colonna Reparto -->
                    <th>Ospedale</th> <!-- Intestazione della colonna Ospedale -->
                    <th>Patologia</th> <!-- Intestazione della colonna Patologia -->
                </tr>
                <?php
                // Popola la tabella con i dati dei ricoveri
                foreach ($ricoveri as $ricovero) {
                    echo "<tr>
                            <td>{$ricovero['data_inizio']}</td>
                            <td>{$ricovero['data_fine']}</td>
                            <td>{$ricovero['numero_stanza']}</td>
                            <td>{$ricovero['piano']}</td>
                            <td>{$ricovero['reparto_nome']}</td>
                            <td>{$ricovero['nome_ospedale']}</td>
                            <td>{$ricovero['patologia']}</td>
                          </tr>";
                }
                ?>
            </table>
        <?php elseif (isset($_GET['cf'])): ?>
            <p>Nessun ricovero trovato per il paziente selezionato.</p> <!-- Messaggio se non ci sono ricoveri per il paziente selezionato -->
        <?php endif; ?>
        <a href="paziente.php" class="btn-back">Torna Indietro</a> <!-- Link per tornare alla pagina principale -->
    </div>
</body>
</html>
