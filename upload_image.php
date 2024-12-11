<?php

$bdd = new PDO('mysql:host=localhost;dbname=film;charset=utf8', 'root', '');



if (!$film || ($_SESSION['username'] !== 'admin' && $film['user_id'] != $_SESSION['user_id'])) {
    echo "Vous n'êtes pas autorisé à ajouter une image pour ce film.";
    exit;
}


if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
        $file = $_FILES['image'];

       
        if ($file['error'] === 0) {
            
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (in_array($fileExtension, $allowedExtensions)) {
                
                $uploadDir = 'uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $filePath = $uploadDir . 'film_' . $id . '.' . $fileExtension;

                if (move_uploaded_file($file['tmp_name'], $filePath)) {
                
                    $update = $bdd->prepare('UPDATE fiche_film SET images = :images WHERE id = :id');
                    $update->execute(['images' => $filePath, 'id' => $id]);

                    header("Location: voir_plus.php?id=$id");
                    exit;
                } else {
                    $error = "Une erreur est survenue lors du déplacement du fichier.";
                }
            } else {
                $error = "Format de fichier non supporté. Extensions autorisées : JPG, JPEG, PNG, GIF.";
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
    <title>Uploader une image</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Uploader une image pour le film</h1>

    <?php if (isset($error)) : ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <label for="image">Choisir une image :</label>
        <input type="file" id="image" name="image" accept="image/*" required>
        <br><br>
        <button type="submit">Uploader</button>
    </form>
</body>
</html>
