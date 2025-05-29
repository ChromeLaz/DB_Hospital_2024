<?php
include '../config.php';

// Funzione per ottenere la lista dei pazienti dal database
function getPazienti() {
    $sql = "SELECT * FROM PAZIENTE"; // Query per selezionare tutti i pazienti
    $result = pg_query($GLOBALS['conn'], $sql); // Esegui la query
    return pg_fetch_all($result); // Restituisci tutti i risultati come array associativo
}

$pazienti = getPazienti(); // Ottieni la lista dei pazienti
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizza Pazienti</title>
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
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .btn {
            padding: 5px 10px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .btn-container {
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Visualizza Pazienti</h1> <!-- Titolo della pagina -->
        <table>
            <tr>
                <th>Codice Fiscale</th> <!-- Intestazione della colonna Codice Fiscale -->
                <th>Nome</th> <!-- Intestazione della colonna Nome -->
                <th>Cognome</th> <!-- Intestazione della colonna Cognome -->
                <th>Data di Nascita</th> <!-- Intestazione della colonna Data di Nascita -->
                <th>Azioni</th> <!-- Intestazione della colonna Azioni -->
            </tr>
            <?php foreach ($pazienti as $paziente): ?>
            <tr>
                <td><?php echo $paziente['cf']; ?></td> <!-- Colonna Codice Fiscale -->
                <td><?php echo $paziente['nome']; ?></td> <!-- Colonna Nome -->
                <td><?php echo $paziente['cognome']; ?></td> <!-- Colonna Cognome -->
                <td><?php echo $paziente['data_nascita']; ?></td> <!-- Colonna Data di Nascita -->
                <td class="btn-container">
                    <a href="modificaPaziente.php?cf=<?php echo $paziente['cf']; ?>" class="btn">Modifica</a> <!-- Pulsante Modifica -->
                    <a href="eliminaPaziente.php?cf=<?php echo $paziente['cf']; ?>" class="btn btn-danger">Elimina</a> <!-- Pulsante Elimina -->
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <br>
        <br>
        
        <a href="paziente.php" class="btn" style="margin-top: 20px;">Torna Indietro</a> <!-- Link per tornare alla pagina principale -->
    </div>
</body>
</html>
