<?php
session_start();


if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php'); 
    exit;
}

$bdd = new PDO('mysql:host=localhost;dbname=film;charset=utf8', 'root', '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['titre'], $_POST['duree'], $_POST['date'])) {
        $titre = htmlspecialchars($_POST['titre']);
        $duree = htmlspecialchars($_POST['duree']);
        $date = htmlspecialchars($_POST['date']);
        $user_id = $_SESSION['user_id']; 

        $request = $bdd->prepare('INSERT INTO fiche_film (titre, duree, date_de_sortie, user_id) VALUES (:titre, :duree, :date, :user_id)');
        $request->execute([
            'titre' => $titre,
            'duree' => $duree,
            'date' => $date,
            'user_id' => $user_id
        ]);

        header('Location: index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un film</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Ajouter un film</h1>
    <form action="" method="POST">
        <label for="titre">Titre :</label>
        <input type="text" id="titre" name="titre" required>
        <br><br>

        <label for="duree">Durée (en minutes) :</label>
        <input type="number" id="duree" name="duree" required>
        <br><br>

        <label for="date">Date de sortie :</label>
        <input type="text" id="date" name="date" required>
        <br><br>

        <button type="submit">Ajouter</button>
    </form>
</body>
</html>
