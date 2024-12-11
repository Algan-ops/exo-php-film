<?php
session_start();


$bdd = new PDO('mysql:host=localhost;dbname=film;charset=utf8', 'root', '');


$request = $bdd->prepare('SELECT * FROM fiche_film');
$request->execute();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des films</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Bienvenue sur notre plateforme de films</h1>
        <?php if (isset($_SESSION['user_id'])) : ?>
            
            <p>Bonjour, <?php ($_SESSION['username']); ?> !</p>
            <a href="deconnexion.php">Se déconnecter</a>

            <?php if ($_SESSION['username'] === 'admin') : ?>
                <p style="color: red;">Vous êtes connecté en tant qu'administrateur.</p>
            <?php endif; ?>
        <?php else : ?>
            
            <a href="connexion.php">Se connecter</a>
            <a href="inscription.php">S'inscrire</a>
        <?php endif; ?>
    </header>

    <h2 class="liste">Liste des films</h2>
    <div class="container">
    <div class="container">
    <?php while ($data = $request->fetch()) : ?>
        <article>
            <p><strong><?php echo htmlspecialchars($data['titre']); ?></strong></p>
            <p>Durée : <?php echo htmlspecialchars($data['duree']); ?> minutes</p>
            <p>Date de sortie : <?php echo htmlspecialchars($data['date_de_sortie']); ?></p>

          
            <a class='voir' href="voir_plus.php?id=<?php echo $data['id']; ?>">Voir plus</a>

            
            <?php if (isset($_SESSION['user_id']) && ($_SESSION['username'] === 'admin' || $data['user_id'] == $_SESSION['user_id'])) : ?>
                <a class='modif' href="modifier.php?id=<?php echo $data['id']; ?>">Modifier</a>
                <a class='supp' href="delet.php?id=<?php echo $data['id']; ?>">Supprimer</a>
            <?php endif; ?>
        </article>
    <?php endwhile; ?>
</div>

    </div>

    <?php if (isset($_SESSION['user_id']) && $_SESSION['username'] === 'admin') : ?>
      
        <a href="add.php">Ajouter un film</a>
    <?php elseif (isset($_SESSION['user_id'])) : ?>
        
        <a href="add.php">Ajouter un film</a>
    <?php endif; ?>
</body>
</html>
