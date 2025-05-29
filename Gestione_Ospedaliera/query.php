<?php
// Include il file di configurazione per la connessione al database
include 'config.php';

// Definisci le query
$queryA = "SELECT viceprimario, COUNT(*) as numero_sostituzioni FROM SOSTITUZIONE GROUP BY viceprimario HAVING COUNT(*) = 1;";
$queryB = "SELECT viceprimario, COUNT(*) as numero_sostituzioni FROM SOSTITUZIONE GROUP BY viceprimario HAVING COUNT(*) >= 2;";
$queryC = "SELECT cf FROM VICEPRIMARIO WHERE cf NOT IN (SELECT viceprimario FROM SOSTITUZIONE);";

// Esegui le query
$resultA = pg_query($conn, $queryA);
$resultB = pg_query($conn, $queryB);
$resultC = pg_query($conn, $queryC);

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interrogazioni Progetto</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            text-align: center;
            padding: 20px;
        }
        table {
            width: 80%;
            margin: auto;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        .button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            background-color: #007BFF;
            color: white;
            margin-top: 20px;
        }
        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h2>Risultati delle Interrogazioni </h2>

    <h3>Vice Primari che hanno sostituito esattamente una volta il proprio primario:</h3>
    <table>
        <tr>
            <th>Viceprimario</th>
            <th>Numero Sostituzioni</th>
        </tr>
        <?php while ($row = pg_fetch_assoc($resultA)) : ?>
        <tr>
            <td><?php echo htmlspecialchars($row['viceprimario']); ?></td>
            <td><?php echo htmlspecialchars($row['numero_sostituzioni']); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h3>Vice Primari che hanno sostituito almeno due volte il proprio primario:</h3>
    <table>
        <tr>
            <th>Viceprimario</th>
            <th>Numero Sostituzioni</th>
        </tr>
        <?php while ($row = pg_fetch_assoc($resultB)) : ?>
        <tr>
            <td><?php echo htmlspecialchars($row['viceprimario']); ?></td>
            <td><?php echo htmlspecialchars($row['numero_sostituzioni']); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h3>Vice Primari che non hanno mai sostituito il proprio primario:</h3>
    <table>
        <tr>
            <th>Viceprimario</th>
        </tr>
        <?php while ($row = pg_fetch_assoc($resultC)) : ?>
        <tr>
            <td><?php echo htmlspecialchars($row['cf']); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <button class="button" onclick="location.href='index.php'">Torna alla Home</button>
</body>
</html>

<?php
// Chiudi la connessione al database
pg_close($conn);
?>
