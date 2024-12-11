<?php

$bdd = new PDO('mysql:host=localhost;dbname=film;charset=utf8', 'root', '');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username'], $_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

       
        if (!empty($username) && !empty($password)) {
           
            $check = $bdd->prepare('SELECT * FROM users WHERE username = :username');
            $check->execute(['username' => $username]);

            if ($check->rowCount() === 0) {
               
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                
                $insert = $bdd->prepare('INSERT INTO users (username, password) VALUES (:username, :password)');
                $insert->execute([
                    'username' => $username,
                    'password' => $hashedPassword
                ]);

                $success = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
            } else {
                $error = "Le nom d'utilisateur existe déjà.";
            }
        } else {
            $error = "Veuillez remplir tous les champs.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Inscription</h1>

    <?php if (isset($error)) : ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if (isset($success)) : ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php endif; ?>

    <form action="" method="POST">
        <label for="username">Nom d'utilisateur :</label>
        <input type="text" id="username" name="username" required>
        <br><br>

        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required>
        <br><br>

        <button type="submit">S'inscrire</button>
    </form>

    <a href="connexion.php">Se connecter</a>
</body>
</html>
