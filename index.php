<?php
session_start();
$conn = new mysqli("localhost", "root", "", "portfolio");
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

$userEmail = null;
$userRole = null;

if (isset($_SESSION["user_id"])) {
    $stmt = $conn->prepare("SELECT email, role FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user = $result->fetch_assoc()) {
        $userEmail = $user["email"];
        $userRole = $user["role"];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil - Portfolio</title>
    <style>
        /* Reset */
        * {
            box-sizing: border-box;
            margin: 0; padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #eee;
        }
        html, body {
            height: 100%;
            background-color: #000;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            background-color: #121212;
            border-radius: 12px;
            padding: 30px 40px;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 0 15px rgba(102, 187, 106, 0.8);
            text-align: center;
        }
        h2 {
            margin-bottom: 25px;
            color: #66bb6a;
            font-weight: 700;
        }
        .info-box, .success-box {
            margin-bottom: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            text-align: left;
            font-size: 14px;
        }
        .info-box {
            background-color: #222;
            border: 1px solid #444;
            color: #ccc;
        }
        .success-box {
            background-color: #183f19;
            border: 1px solid #66bb6a;
            color: #a6d785;
            font-weight: 600;
        }
        .btn-container {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 15px;
        }
        a.button {
            display: block;
            padding: 12px 0;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            color: #121212;
            background-color: #66bb6a;
            box-shadow: 0 4px 10px rgba(102, 187, 106, 0.7);
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        a.button:hover {
            background-color: #4ca33e;
            color: #fff;
        }
        a.link {
            color: #66bb6a;
            font-weight: 600;
            text-decoration: none;
            margin: 0 5px;
            transition: color 0.3s ease;
        }
        a.link:hover {
            text-decoration: underline;
            color: #a6d785;
        }
        .profile-logout {
            margin-top: 10px;
            font-size: 14px;
        }
        .profile-logout span {
            margin: 0 8px;
            color: #666;
        }
        @media (max-width: 480px) {
            .container {
                max-width: 100%;
                padding: 20px;
            }
            a.button {
                font-size: 16px;
                padding: 14px 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Bienvenue sur le Portfolio</h2>

        <?php if (!$userEmail): ?>
            <div class="info-box">
                👋 Bienvenue invité<br>
                Veuillez vous connecter ou vous inscrire pour accéder à toutes les fonctionnalités.<br>
                </div>
            <div class="btn-container">
                <a href="login.php" class="button">🔐 Se connecter</a>
                <a href="register.php" class="button">📝 S'inscrire</a>
            </div>

        <?php elseif ($userRole === "admin"): ?>
            <div class="success-box">
                ✅ Connecté en tant qu’administrateur : <strong><?= htmlspecialchars($userEmail) ?></strong>
            </div>

            <div class="btn-container">
                <a href="admin.php" class="button">🛠️ Panneau d’administration</a>
                <a href="admin_user_skills.php" class="button">📋 Voir les compétences utilisateurs</a>
                <a href="admin_projects.php" class="button">📁 Gérer les projets</a>
                <a href="admin_skills.php" class="button">🛠️ Gérer les compétences</a>
            </div>

            <div class="profile-logout">
                <a href="profile.php" class="link">👤 Mon profil</a>
                <span>|</span>
                <a href="logout.php" class="link">Se déconnecter</a>
            </div>

        <?php else: ?>
            <div class="info-box">
                👤 Bienvenue <strong><?= htmlspecialchars($userEmail) ?></strong>
            </div>

            <div class="btn-container">
                <a href="projects.php" class="button">📁 Mes projets</a>
                <a href="user_skills.php" class="button">🎓 Mes compétences</a>
            </div>

            <div class="profile-logout">
                <a href="profile.php" class="link">👤 Mon profil</a>
                <span>|</span>
                <a href="logout.php" class="link">Se déconnecter</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
