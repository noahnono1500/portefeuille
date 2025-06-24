<?php
session_start();
$conn = new mysqli("localhost", "root", "", "portfolio");
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Vérification administrateur
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

// Récupérer utilisateurs actifs
$result = $conn->query("SELECT id, email, role FROM users WHERE is_active = 1 ORDER BY email ASC");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Gestion des utilisateurs - Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: #eee;
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 900px;
            margin: auto;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            background: #222;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 15px rgba(0, 255, 0, 0.3);
        }
        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #444;
            text-align: left;
            word-break: break-word;
        }
        th {
            background-color: #333;
        }
        tr:hover {
            background-color: #333;
        }
        a {
            color: #7ddb4e;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
        .header {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        .header a {
            background: #7ddb4e;
            padding: 10px 20px;
            border-radius: 5px;
            color: #000;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Utilisateurs actifs</h1>
            <div>
                <a href="admin.php">Tableau de bord</a>
                <a href="admin_inactive_users.php">Utilisateurs désactivés</a>
            </div>
        </div>

        <table>
            <tr>
                <th>Email</th>
                <th>Rôle</th>
                <th>Actions</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['role']) ?></td>
                <td>
                    <a href="edit_user.php?id=<?= $row['id'] ?>">Modifier</a> |
                    <a href="confirm_delete_user.php?id=<?= $row['id'] ?>">Désactiver</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
