<?php
session_start();
$conn = new mysqli("localhost", "root", "", "portfolio");
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

if (!isset($_SESSION["user_id"])) {
    die("<p style='color: #f44336; text-align:center; margin-top:50px;'>⛔ Veuillez <a href='login.php' style='color:#2196f3;'>vous connecter</a> pour ajouter un projet.</p>");
}

$user_id = $_SESSION["user_id"];

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $image_url = trim($_POST["image_url"]);

    if (!$title || !$description || !$image_url) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        $sql = "INSERT INTO projects (user_id, title, description, image_url) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $user_id, $title, $description, $image_url);
        if ($stmt->execute()) {
            $success = "Projet ajouté avec succès !";
        } else {
            $error = "Erreur lors de l'ajout du projet : " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Ajouter un projet - Portfolio</title>
    <style>
        body {
            background-color: #121212;
            color: #e0e0e0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 40px 20px;
        }
        .container {
            background: #1e1e1e;
            border-radius: 15px;
            padding: 30px 35px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.6);
        }
        h2 {
            color: #4caf50;
            margin-bottom: 25px;
            text-align: center;
            text-shadow: 0 0 8px #4caf50;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 10px;
            border: none;
            font-size: 15px;
            box-sizing: border-box;
            background-color: #2c2c2c;
            color: #e0e0e0;
            transition: box-shadow 0.3s ease;
            resize: vertical;
        }
        input[type="text"]:focus, textarea:focus {
            outline: none;
            box-shadow: 0 0 8px #4caf50;
        }
        textarea {
            min-height: 120px;
        }
        input[type="submit"] {
            background-color: #4caf50;
            color: white;
            border: none;
            padding: 14px 20px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            width: 100%;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #388e3c;
        }
        .message {
            margin-bottom: 20px;
            padding: 12px 15px;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
        }
        .error {
            background-color: #f44336;
            color: white;
            box-shadow: 0 0 10px #f44336;
        }
        .success {
            background-color: #81c784;
            color: #1b5e20;
            box-shadow: 0 0 10px #81c784;
        }
        a.back-link {
            display: block;
            margin-top: 15px;
            color: #80cbc4;
            text-align: center;
            text-decoration: none;
            font-weight: 500;
        }
        a.back-link:hover {
            text-decoration: underline;
            color: #4db6ac;
        }
        @media (max-width: 480px) {
            body {
                padding: 20px 10px;
            }
            .container {
                padding: 25px 20px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Ajouter un projet</h2>

    <?php if ($error): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="message success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="project_create.php" novalidate>
        <input type="text" name="title" placeholder="Titre du projet" required autocomplete="off" />
        <textarea name="description" placeholder="Description du projet" required></textarea>
        <input type="text" name="image_url" placeholder="URL de l'image" required autocomplete="off" />
        <input type="submit" value="Ajouter le projet" />
    </form>
    <a href="projects.php" class="back-link">← Retour à mes projets</a>
</div>
</body>
</html>
