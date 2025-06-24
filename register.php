<?php
session_start();
$conn = new mysqli("localhost", "root", "", "portfolio");
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if (!$email) {
        $error = "Adresse email invalide.";
    } elseif (strlen($password) < 6) {
        $error = "Le mot de passe doit contenir au moins 6 caractères.";
    } elseif ($password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        // Vérifier si email déjà utilisé
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = "Cette adresse email est déjà utilisée.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = "user"; // rôle par défaut

            $stmt = $conn->prepare("INSERT INTO users (email, password, role, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("sss", $email, $hashed_password, $role);
            if ($stmt->execute()) {
                $success = "Inscription réussie. <a href='login.php'>Connectez-vous ici</a>.";
            } else {
                $error = "Erreur lors de l'inscription : " . $conn->error;
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Inscription</title>
    <link rel="stylesheet" href="style.css" />
    <style>
        body {
            background-color: #111;
            color: #eee;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        form {
            background-color: #222;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.7);
            width: 360px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #fff;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            margin: 12px 0;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            outline: none;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #28a745;
            color: white;
            font-weight: bold;
            border: none;
            padding: 14px;
            width: 100%;
            border-radius: 10px;
            font-size: 17px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #218838;
        }
        .error, .success {
            margin-top: 15px;
            padding: 10px;
            border-radius: 8px;
        }
        .error {
            background-color: #fcebea;
            color: #b71c1c;
            border: 1px solid #f5c6cb;
            box-shadow: 0 0 10px rgba(255, 0, 0, 0.1);
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            box-shadow: 0 0 10px rgba(0, 255, 0, 0.1);
        }
        a {
            color: #56a5fa;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<form method="POST" action="register.php">
    <h2>Inscription</h2>

    <input type="email" name="email" placeholder="Adresse email" required autofocus />
    <input type="password" name="password" placeholder="Mot de passe (min 6 caractères)" required />
    <input type="password" name="confirm_password" placeholder="Confirmer le mot de passe" required />

    <input type="submit" value="S'inscrire" />

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="success"><?= $success ?></div>
    <?php endif; ?>
    
    <p style="margin-top: 20px;">
        <a href="login.php">Déjà inscrit ? Connectez-vous</a>
    </p>
</form>
</body>
</html>
