<?php
session_start();
$conn = new mysqli("localhost", "root", "", "portfolio");
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Vérifier que l'utilisateur est admin
if (!isset($_SESSION["user_id"])) {
    die("Accès refusé. <a href='login.php'>Se connecter</a>");
}
$user_id = $_SESSION["user_id"];
$stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($role);
$stmt->fetch();
$stmt->close();
if ($role !== "admin") {
    die("Accès réservé aux administrateurs.");
}

// Récupérer l'utilisateur à modifier
if (!isset($_GET["id"])) {
    die("ID utilisateur manquant.");
}
$edit_id = intval($_GET["id"]);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_email = $_POST["email"];
    $new_role = $_POST["role"];
    
    $stmt = $conn->prepare("UPDATE users SET email = ?, role = ? WHERE id = ?");
    $stmt->bind_param("ssi", $new_email, $new_role, $edit_id);
    if ($stmt->execute()) {
        header("Location: admin_users.php");
        exit;
    } else {
        $error = "Erreur lors de la mise à jour.";
    }
    $stmt->close();
}

// Charger les infos actuelles
$stmt = $conn->prepare("SELECT email, role FROM users WHERE id = ?");
$stmt->bind_param("i", $edit_id);
$stmt->execute();
$stmt->bind_result($email, $role);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un utilisateur</title>
    <style>
        body {
            background-color: #121212;
            color: #eee;
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .form-container {
            max-width: 500px;
            margin: auto;
            background: #222;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,255,0,0.3);
        }
        label, input, select {
            display: block;
            width: 100%;
            margin-bottom: 15px;
        }
        input, select {
            padding: 10px;
            border: none;
            border-radius: 4px;
        }
        button {
            background: #7ddb4e;
            color: #000;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            opacity: 0.9;
        }
        a {
            color: #7ddb4e;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Modifier l'utilisateur</h1>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="post">
            <label for="email">Email :</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($email) ?>" required>

            <label for="role">Rôle :</label>
            <select name="role" id="role" required>
                <option value="user" <?= $role === 'user' ? 'selected' : '' ?>>Utilisateur</option>
                <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Administrateur</option>
            </select>

            <button type="submit">Enregistrer</button>
        </form>
        <p><a href="admin_users.php">Retour</a></p>
    </div>
</body>
</html>
