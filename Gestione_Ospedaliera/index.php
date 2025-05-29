<?php
include 'config.php';
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina Principale - Sistema Sanitario</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .navbar {
            width: 100%;
            background-color: #333;
            color: #fff;
            padding: 10px 0;
            text-align: center;
            font-size: 18px;
        }
        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        .option {
            margin: 15px;
            padding: 20px;
            width: 200px;
            text-align: center;
            background-color: #007bff;
            border-radius: 8px;
            color: #fff;
            text-decoration: none;
            transition: background-color 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .option:hover {
            background-color: #0056b3;
        }
        h1 {
            margin-top: 40px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="navbar">
        Sistema Sanitario - Pannello di Controllo
    </div>

    <h1>Benvenuto nel Sistema Sanitario</h1>

    <div class="container">
        <a href="Ospedale/ospedale.php" class="option">Struttura Ospedaliera</a>
        <a href="Reparto/reparto.php" class="option">Reparto</a>
        <a href="Personale/personale.php" class="option">Personale</a>
        <a href="Esame/esame.php" class="option">Esame</a>
        <a href="Paziente/paziente.php" class="option">Paziente</a>
        <a href="Prenotazione/prenotazione.php" class="option">Prenotazione</a>
        <a href="Pronto_Soccorso/turniProntoSoccorso.php" class="option">Turni PS</a>
        <a href="Ambulatori/ambulatorio.php" class="option">Ambulatori</a>       
        <a href="query.php" class="option">Interrogazioni progetto</a>
    </div>
</body>
</html>
