<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Ospedali</title>
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
        h1 {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestione Ospedali</h1>
        <a href="aggiungiOspedale.php" class="option">Aggiungi Ospedale</a>
        <a href="visualizzaOspedali.php" class="option">Visualizza/Modifica Ospedali</a>
        <a href="eliminaOspedale.php" class="option">Elimina Ospedale</a>
        <div class="back-button">
            <a href="../index.php">Torna alla Home</a>
        </div>
</body>
</html>
