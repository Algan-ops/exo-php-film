<?php
session_start();
$bdd = new PDO('mysql:host=localhost;dbname=film;charset=utf8', 'root', '');

// Vérifier si un ID de film est passé en paramètre GET
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Récupérer les informations du film
    $request = $bdd->prepare('SELECT * FROM fiche_film WHERE id = :id');
    $request->execute(['id' => $id]);
    $film = $request->fetch();

    // Vérifier si le film existe
    if (!$film) {
        echo "Film introuvable.";
        exit;
    }

    // Vérifier si l'utilisateur est autorisé (admin ou créateur du film)
    if ($_SESSION['username'] !== 'admin' && $film['user_id'] != $_SESSION['user_id']) {
        echo "Vous n'êtes pas autorisé à modifier ce film.";
        exit;
    }

    // Traiter le formulaire de modification (titre, durée, date)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titre'], $_POST['duree'], $_POST['date'])) {
        $titre = htmlspecialchars($_POST['titre']);
        $duree = htmlspecialchars($_POST['duree']);
        $date = htmlspecialchars($_POST['date']);

        // Mettre à jour les informations du film
        $update = $bdd->prepare('UPDATE fiche_film SET titre = :titre, duree = :duree, date_de_sortie = :date WHERE id = :id');
        $update->execute([
            'titre' => $titre,
            'duree' => $duree,
            'date' => $date,
            'id' => $id
        ]);

        $success = "Les informations du film ont été mises à jour.";
    }

    // Traiter l'upload d'image
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
        $file = $_FILES['image'];

        // Vérifier si le fichier a été uploadé sans erreur
        if ($file['error'] === 0) {
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (in_array($fileExtension, $allowedExtensions)) {
                // Définir le dossier d'upload
                $uploadDir = 'uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $filePath = $uploadDir . 'film_' . $id . '.' . $fileExtension;

                // Déplacer le fichier uploadé
                if (move_uploaded_file($file['tmp_name'], $filePath)) {
                    // Mettre à jour la colonne `images` dans la base de données
                    $update = $bdd->prepare('UPDATE fiche_film SET images = :images WHERE id = :id');
                    $update->execute(['images' => $filePath, 'id' => $id]);

                    $success = "L'image du film a été mise à jour.";
                } else {
                    $error = "Une erreur est survenue lors du déplacement du fichier.";
                }
            } else {
                $error = "Format de fichier non supporté. Extensions autorisées : JPG, JPEG, PNG, GIF, WEBP.";
            }
        } else {
            $error = "Une erreur est survenue lors de l'upload.";
        }
    }
} else {
    echo "Aucun film sélectionné.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un film</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Modifier un film</h1>

    <?php if (isset($success)) : ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php endif; ?>
    <?php if (isset($error)) : ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <!-- Formulaire pour modifier les informations -->
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="titre">Titre :</label>
        <input type="text" id="titre" name="titre" value="<?php echo htmlspecialchars($film['titre']); ?>" required>
        <br><br>

        <label for="duree">Durée (en minutes) :</label>
        <input type="number" id="duree" name="duree" value="<?php echo htmlspecialchars($film['duree']); ?>" required>
        <br><br>

        <label for="date">Date de sortie :</label>
        <input type="text" id="date" name="date" value="<?php echo htmlspecialchars($film['date_de_sortie']); ?>" required>
        <br><br>

        <!-- Formulaire pour uploader une image -->
        <label for="image">Ajouter ou modifier une image :</label>
        <input type="file" id="image" name="image" accept="image/*">
        <br><br>

        <button type="submit">Enregistrer les modifications</button>
    </form>

    <?php if (!empty($film['images'])) : ?>
        <h2>Image actuelle</h2>
        <img src="<?php echo htmlspecialchars($film['images']); ?>" alt="Image du film" style="max-width: 300px; max-height: 300px;">
    <?php endif; ?>
    <br><br>

    <a href="index.php">Retour à la liste</a>
</body>
</html>
