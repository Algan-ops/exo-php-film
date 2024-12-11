<?php
session_start();
$bdd = new PDO('mysql:host=localhost;dbname=film;charset=utf8', 'root', '');

// Vérifier si un ID de film est passé
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

    // Vérifier si le formulaire a été soumis pour l'upload d'une image
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
        $file = $_FILES['image'];

        // Vérifier si le fichier a été uploadé sans erreur
        if ($file['error'] === 0) {
            // Vérifier l'extension du fichier
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

                    // Rediriger pour éviter la resoumission du formulaire
                    header("Location: voir_plus.php?id=$id");
                    exit;
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
    <title>Détails du film</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Détails du film</h1>
    <p><strong>Titre :</strong> <?php echo htmlspecialchars($film['titre']); ?></p>
    <p><strong>Durée :</strong> <?php echo htmlspecialchars($film['duree']); ?> minutes</p>
    <p><strong>Date de sortie :</strong> <?php echo htmlspecialchars($film['date_de_sortie']); ?></p>

    <?php if (!empty($film['images'])) : ?>
        <p><strong>Image :</strong></p>
        <img src="<?php echo htmlspecialchars($film['images']); ?>" alt="Image du film" style="max-width: 300px; max-height: 300px;">
    <?php else : ?>
        <p>Aucune image disponible.</p>
    <?php endif; ?>
    <br><br>
    

    <a href="index.php">Retour à la liste</a>
</body>
</html>
