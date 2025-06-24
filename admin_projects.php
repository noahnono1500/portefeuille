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

// Supprimer un projet si demandé via GET (avec confirmation via JS)
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_projects.php");
    exit;
}

// Récupérer tous les projets
$result = $conn->query("SELECT * FROM projects ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Gestion des projets - Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: #eee;
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 1200px;
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
        .btn-delete {
            color: #e74c3c;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-delete:hover {
            text-decoration: underline;
        }
        .header {
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .header a {
            background: #7ddb4e;
            padding: 10px 20px;
            border-radius: 5px;
            color: #121212;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s ease;
        }
        .header a:hover {
            background: #5aac39;
        }
        img {
            max-width: 100px;
            height: auto;
            border-radius: 5px;
        }
        @media (max-width: 768px) {
            th, td {
                padding: 8px 10px;
            }
            .header {
                flex-direction: column;
            }
            .header a {
                width: 100%;
                text-align: center;
            }
            img {
                max-width: 80px;
            }
        }
    </style>
    <script>
        function confirmDelete(id) {
            if (confirm("Voulez-vous vraiment supprimer ce projet ?")) {
                window.location.href = "admin_projects.php?delete=" + id;
            }
        }
    </script>
</head>
<body>
<div class="container">
    <div class="header">
        <a href="project_create.php">➕ Ajouter un projet</a>
        <a href="admin.php">← Retour au panneau admin</a>
    </div>

    <h1>Gestion des projets</h1>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Titre</th>
            <th>Description</th>
            <th>Image</th>
            <th>Créé le</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows === 0): ?>
            <tr><td colspan="6" style="text-align:center;">Aucun projet trouvé.</td></tr>
        <?php else: ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= htmlspecialchars($row['description']) ?></td>
                    <td>
                        <?php if ($row['image_url']): ?>
                            <img src="<?= htmlspecialchars($row['image_url']) ?>" alt="Image projet" />
                        <?php endif; ?>
                    </td>
                    <td><?= $row['created_at'] ?></td>
                    <td>
                        <a href="project_edit.php?id=<?= $row['id'] ?>">Modifier</a> |
                        <span class="btn-delete" onclick="confirmDelete(<?= $row['id'] ?>)">Supprimer</span>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
