<?php
$bdd = new PDO('mysql:host=localhost;dbname=film;charset=utf8', 'root', '');


$check = $bdd->prepare('SELECT * FROM fiche_film WHERE id = :id');
$check->execute(['id' => $id]);
$film = $check->fetch();

if (!$film || ($_SESSION['username'] !== 'admin' && $film['user_id'] != $_SESSION['user_id'])) {
    echo "Vous n'êtes pas autorisé à supprimer ce film.";
    exit;
}


$check = $bdd->prepare('SELECT * FROM fiche_film WHERE id = :id AND user_id = :user_id');
$check->execute(['id' => $id, 'user_id' => $_SESSION['user_id']]);

if ($check->rowCount() === 0) {
    echo "Vous n'êtes pas autorisé à supprimer cette fiche.";
    exit;
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id']; 
    $request = $bdd->prepare('DELETE FROM fiche_film WHERE id = :id');
    $request->execute(['id' => $id]);
    header('Location: index.php');
    exit;
} else {
    echo "Aucun film sélectionné pour suppression.";
}
?>
