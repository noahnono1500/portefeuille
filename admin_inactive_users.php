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

// Récupérer utilisateurs désactivés
$result = $conn->query("SELECT id, email, role FROM users WHERE is_active = 0 ORDER BY email ASC");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Utilisateurs désactivés</title>
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
            width: 100%;
            border-collapse: collapse;
            background: #222;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 15px rgba(255, 0, 0, 0.3);
        }
        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #444;
            text-align: left;
        }
        th {
            background-color: #333;
        }
        tr:hover {
            background-color: #333;
        }
        a {
            color: #7ddb4e;
            font-weight: bold;
            text-decoration: none;
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
            <h1>Utilisateurs désactivés</h1>
            <a href="admin_users.php">Utilisateurs actifs</a>
        </div>

        <table>
            <tr>
                <th>Email</th>
                <th>Rôle</th>
                <th>Action</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['role']) ?></td>
                <td>
                    <a href="reactivate_user.php?id=<?= $row['id'] ?>" onclick="return confirm('Réactiver cet utilisateur ?')">Réactiver</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
