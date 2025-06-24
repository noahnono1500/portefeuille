<?php
session_start();
$conn = new mysqli("localhost", "root", "", "portfolio");
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Vérification rôle admin
if (!isset($_SESSION["user_id"])) {
    die("Accès refusé. <a href='login.php'>Se connecter</a>");
}
$user_id = $_SESSION["user_id"];
$stmt = $conn->prepare("SELECT email, role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    if ($row["role"] !== "admin") {
        die("Accès réservé à l'administrateur.");
    }
    $admin_email = $row["email"];
} else {
    die("Utilisateur non trouvé.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Panneau d'administration</title>
    <style>
        body {
            background-color: #111;
            color: #eee;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0; padding: 20px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        h1 {
            margin-bottom: 25px;
            color: #1abc9c;
            text-align: center;
        }
        .welcome {
            margin-bottom: 40px;
            font-size: 18px;
            font-weight: 600;
            color: #eee;
            text-align: center;
        }
        .btn-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            max-width: 700px;
            width: 100%;
        }
        a.btn {
            background-color: #17a2b8;
            color: white;
            text-decoration: none;
            padding: 15px 25px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 16px;
            box-shadow: 0 4px 10px rgba(23, 162, 184, 0.5);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            flex: 1 1 200px;
            text-align: center;
        }
        a.btn:hover {
            background-color: #138496;
            box-shadow: 0 6px 15px rgba(19, 132, 150, 0.7);
        }
        .logout-link {
            margin-top: 40px;
            color: #e74c3c;
            font-weight: 600;
            text-decoration: none;
        }
        .logout-link:hover {
            text-decoration: underline;
        }
        @media (max-width: 500px) {
            a.btn {
                flex: 1 1 100%;
                padding: 12px 20px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <h1>🔧 Panneau d'administration</h1>
    <p class="welcome">Bienvenue, <strong><?= htmlspecialchars($admin_email) ?></strong></p>

    <div class="btn-container">
        <a href="admin_users.php" class="btn">👥 Gestion des utilisateurs</a>
        <a href="admin_projects.php" class="btn">📁 Gestion des projets</a>
        <a href="admin_skills.php" class="btn">🛠️ Gestion des compétences</a>
        <a href="admin_user_skills.php" class="btn">📊 Compétences utilisateurs</a>
    </div>

    <a href="index.php" class="logout-link">← Retour à l'accueil</a>
</body>
</html>
