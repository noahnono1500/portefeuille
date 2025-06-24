<?php
session_start();
$conn = new mysqli("localhost", "root", "", "portfolio");
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname = htmlspecialchars(trim($_POST["fullname"] ?? ""));

    if (empty($fullname)) {
        $error = "Le nom complet ne peut pas être vide.";
    } else {
        $stmt = $conn->prepare("UPDATE users SET fullname = ? WHERE id = ?");
        $stmt->bind_param("si", $fullname, $user_id);
        if ($stmt->execute()) {
            $success = "Profil mis à jour avec succès.";
        } else {
            $error = "Erreur lors de la mise à jour.";
        }
        $stmt->close();
    }
}

// Récupérer les infos utilisateur
$stmt = $conn->prepare("SELECT email, fullname FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Mon profil</title>
<style>
    body {
        background-color: #111;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #ddd;
        margin: 0;
        padding: 20px;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        min-height: 100vh;
    }
    .profile-container {
        background-color: #222;
        padding: 25px 30px;
        border-radius: 10px;
        box-shadow: 0 0 15px #0f0;
        max-width: 400px;
        width: 100%;
    }
    h2 {
        color: #0f0;
        margin-bottom: 15px;
        border-bottom: 2px solid #0f0;
        padding-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    h2::before {
        content: "👤";
    }
    input[type="email"],
    input[type="text"] {
        width: 100%;
        padding: 10px 12px;
        margin: 10px 0 15px 0;
        border: 1px solid #444;
        border-radius: 5px;
        background-color: #111;
        color: #ddd;
        font-size: 16px;
        box-sizing: border-box;
        transition: border-color 0.3s ease;
    }
    input[type="email"]:focus,
    input[type="text"]:focus {
        border-color: #0f0;
        outline: none;
    }
    input[readonly] {
        background-color: #222;
        cursor: default;
    }
    button, input[type="submit"] {
        background-color: #0f0;
        border: none;
        color: #000;
        font-weight: bold;
        padding: 10px 20px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 16px;
        width: 100%;
        transition: background-color 0.3s ease;
    }
    button:hover, input[type="submit"]:hover {
        background-color: #0c0;
    }
    .link {
        display: block;
        margin-top: 15px;
        color: #0f0;
        text-decoration: none;
        font-size: 14px;
        text-align: center;
        transition: color 0.3s ease;
    }
    .link:hover {
        color: #5f5;
    }
    .messages {
        margin-bottom: 15px;
        font-weight: bold;
        font-size: 14px;
        text-align: center;
    }
    .error {
        color: #f66;
    }
    .success {
        color: #6f6;
    }
    @media (max-width: 450px) {
        body {
            padding: 15px;
        }
        .profile-container {
            padding: 20px;
            max-width: 100%;
        }
        h2 {
            font-size: 20px;
        }
        input[type="email"],
        input[type="text"],
        button, input[type="submit"] {
            font-size: 14px;
            padding: 9px 12px;
        }
    }
</style>
</head>
<body>
<div class="profile-container">
    <h2>Mon profil</h2>
    <?php if ($error): ?>
        <p class="messages error"><?= htmlspecialchars($error) ?></p>
    <?php elseif ($success): ?>
        <p class="messages success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <form method="POST" action="profile.php" novalidate>
        <label for="email">Email</label>
        <input type="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" readonly />

        <label for="fullname">Nom complet</label>
        <input type="text" id="fullname" name="fullname" value="<?= htmlspecialchars($user['fullname']) ?>" placeholder="Nom complet" required />

        <input type="submit" value="Mettre à jour" />
    </form>
    <a href="reset_password.php" class="link">🔐 Changer mon mot de passe</a>
    <a href="index.php" class="link">🏠 Retour à l'accueil</a>
</div>
</body>
</html>
