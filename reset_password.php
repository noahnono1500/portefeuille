<?php
session_start();
$conn = new mysqli("localhost", "root", "", "portfolio");
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

$error = $success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL);
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    if (!$email) {
        $error = "Adresse email invalide.";
    } elseif (empty($new_password) || empty($confirm_password)) {
        $error = "Veuillez remplir tous les champs.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        // Vérifier si l'email existe
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            // Mettre à jour le mot de passe
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            $update->bind_param("ss", $hashed_password, $email);
            if ($update->execute()) {
                $success = "Mot de passe mis à jour avec succès.";
            } else {
                $error = "Erreur lors de la mise à jour.";
            }
            $update->close();
        } else {
            $error = "Aucun utilisateur trouvé avec cet email.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialiser mot de passe</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background-color: #121212;
            color: #eee;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #1e1e1e;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 0 10px #00ff00aa;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        h2 {
            color: #00cc44;
            margin-bottom: 20px;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px 12px;
            margin: 10px 0 15px 0;
            border-radius: 6px;
            border: 1px solid #444;
            background-color: #222;
            color: #eee;
            font-size: 14px;
        }
        input[type="submit"] {
            background-color: #00cc44;
            color: #121212;
            border: none;
            padding: 12px 0;
            width: 100%;
            font-weight: bold;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #009933;
        }
        .message {
            margin: 15px 0;
            padding: 10px;
            border-radius: 6px;
        }
        .error {
            background-color: #cc4444;
            color: #fff;
        }
        .success {
            background-color: #228833;
            color: #fff;
        }
        a {
            color: #00cc44;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Réinitialiser mot de passe</h2>
    <?php if ($error): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="message success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <form method="POST" action="reset_password.php">
        <input type="email" name="email" placeholder="Adresse email" required>
        <input type="password" name="new_password" placeholder="Nouveau mot de passe" required>
        <input type="password" name="confirm_password" placeholder="Confirmer mot de passe" required>
        <input type="submit" value="Changer le mot de passe">
    </form>
    <p><a href="login.php">← Retour à la connexion</a></p>
</div>
</body>
</html>