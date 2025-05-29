<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Prenotazioni</title>
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
        .btn-back {
            padding: 10px 20px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }
        .btn-back:hover {
            background-color: #0056b3;
        }
        h1 {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestione Prenotazioni</h1>
        <a href="aggiungiPrenotazione.php" class="option">Aggiungi Prenotazione</a>
        <a href="modificaPrenotazione.php" class="option">Modifica Prenotazione</a>
        <a href="eliminaPrenotazione.php" class="option">Elimina Prenotazione</a>
        <a href="visualizzaPrenotazioni.php" class="option"> Visualizza Prenotazioni</a>

        <br>
        <a href="../index.php" class="btn-back">Torna Indietro</a>
    </div>
</body>
</html>
