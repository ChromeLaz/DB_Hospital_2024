<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Esami</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f0f0f0;
        }
        .container {
            margin-top: 50px;
        }
        .option {
            margin: 20px;
            padding: 20px;
            display: inline-block;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-decoration: none;
            color: #333;
        }
        .option:hover {
            background-color: #f9f9f9;
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
        <h1>Gestione Esami</h1>
        <a href="inserisciEsame.php" class="option">Inserisci Esame</a>
        <a href="modificaEsame.php" class="option">Modifica Esame</a>
        <a href="visualizzaEsami.php" class="option">Visualizza Esami</a>
        <a href="eliminaEsame.php" class="option">Elimina Esame</a>
        <div class="back-button">
            <a href="../index.php">Torna alla pagina principale</a>
        </div>
    </div>
</body>
</html>
