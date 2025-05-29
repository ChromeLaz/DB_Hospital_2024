<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Personale</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f0f0f0;
        }
        .container {
            margin-top: 100px;
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
        h1 {
            color: #333;
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
        <h1>Gestione Personale</h1>
        <a href="inserisciPersonale.php" class="option">Inserisci Personale</a>
        <a href="visualizzaPersonale.php" class="option">Visualizza Personale</a>
        <a href="modificaPersonale.php" class="option">Modifica Personale</a>
        <a href="eliminaPersonale.php" class="option">Elimina Personale</a>
        <div class="back-button">
            <a href="../index.php">Torna alla pagina principale</a>
        </div>
    </div>
</body>
</html>
