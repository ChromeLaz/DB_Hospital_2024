<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Pazienti</title>
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
        <h1>Gestione Pazienti</h1>
        <a href="aggiungiPaziente.php" class="option">Aggiungi Paziente</a>
        <a href="visualizzaPazienti.php" class="option">Visualizza/Modifica/Elimina Pazienti</a>
        <a href="visualizzaPazientiPrenotazioni.php" class="option">Visualizza Pazienti e Prenotazioni</a>
        <a href="storicoRicoveri.php" class="option">Storico Ricoveri</a>
        <br>
        <a href="../index.php" class="btn-back">Torna Indietro</a>
    </div>
</body>
</html>
