<?php
include '../config.php'; // Includiamo il file di configurazione del database per connetterci

// Prepariamo la query SQL per ottenere tutti gli ospedali e i relativi pronto soccorso
$query = "SELECT OSPEDALE.codice, OSPEDALE.nome, OSPEDALE.città, OSPEDALE.indirizzo, PRONTOSOCCORSO.nome AS nome_pronto_soccorso
          FROM OSPEDALE
          LEFT JOIN PRONTOSOCCORSO ON OSPEDALE.codice = PRONTOSOCCORSO.ospedale";
// Eseguiamo la query utilizzando pg_query
$result = pg_query($conn, $query);
?>

<!DOCTYPE html> 
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizza Ospedali</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f0f0f0;
        }
        table {
            width: 80%;
            border-collapse: collapse;
            margin: 0 auto;
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
        <h1>Visualizza Ospedali</h1>
        <?php if (pg_num_rows($result) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Codice</th>
                        <th>Nome</th>
                        <th>Città</th>
                        <th>Indirizzo</th>
                        <th>Pronto Soccorso</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Cicliamo sui risultati della query per popolare la tabella -->
                    <?php while ($row = pg_fetch_assoc($result)) : ?>
                    <tr>
                        <td><?php echo $row['codice']; ?></td>
                        <td><?php echo $row['nome']; ?></td>
                        <td><?php echo $row['città']; ?></td>
                        <td><?php echo $row['indirizzo']; ?></td>
    <!-- condizione ? espressione_se_vera : espressione_se_falsa; Se esiste nome pronto soccorso viene segnato SI ! -->
                        <td><?php echo $row['nome_pronto_soccorso'] ? 'Sì' : 'No'; ?></td>
                        <!-- Link per modificare i dati dell'ospedale -->
                        <td><a href="modificaOspedale.php?codice=<?php echo $row['codice']; ?>">Modifica</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <!-- Mostra un messaggio se non ci sono ospedali nel sistema -->
            <div class="message">
                <h2>Nessun ospedale presente nel sistema.</h2>
            </div>
        <?php endif; ?>
        <div class="back-button">
            <a href="ospedale.php">Torna alla pagina principale</a> <!-- Link per tornare alla pagina principale -->
        </div>
    </div>
</body>
</html>
