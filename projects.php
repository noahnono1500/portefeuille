<?php
session_start();
$conn = new mysqli("localhost", "root", "", "portfolio");
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION["user_id"])) {
    die("<p style='color:red;'>⛔ Veuillez <a href='login.php'>vous connecter</a> pour accéder à vos projets.</p>");
}

$user_id = $_SESSION["user_id"];

// Traitement de l'ajout de projet
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["title"])) {
    $title = htmlspecialchars($_POST["title"]);
    $description = htmlspecialchars($_POST["description"]);
    $image_url = htmlspecialchars($_POST["image_url"]);

    $sql = "INSERT INTO projects (user_id, title, description, image_url) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $user_id, $title, $description, $image_url);
    $stmt->execute();
    $stmt->close();
}

// Récupérer les projets de l'utilisateur
$stmt = $conn->prepare("SELECT id, title, description, image_url, created_at FROM projects WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes projets</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Styles basiques, adapte selon ton CSS global */
        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
            background: #111;
            border-radius: 10px;
            color: #ddd;
            font-family: Arial, sans-serif;
        }
        h2 {
            color: #5cb85c;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #444;
            text-align: left;
        }
        th {
            background-color: #222;
            color: #5cb85c;
        }
        tr:hover {
            background-color: #222;
        }
        a {
            color: #5cb85c;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        form input[type="text"],
        form textarea {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border-radius: 6px;
            border: 1px solid #333;
            background-color: #222;
            color: #eee;
        }
        form textarea {
            resize: vertical;
        }
        form input[type="submit"] {
            background-color: #5cb85c;
            color: white;
            border: none;
            padding: 12px 25px;
            cursor: pointer;
            border-radius: 6px;
            font-weight: bold;
        }
        form input[type="submit"]:hover {
            background-color: #4cae4c;
        }
        img {
            max-width: 100px;
            border-radius: 6px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>📁 Mes projets</h2>

    <form method="POST" action="projects.php">
        <h3>Ajouter un projet</h3>
        <input type="text" name="title" placeholder="Titre du projet" required>
        <textarea name="description" placeholder="Description" required></textarea>
        <input type="text" name="image_url" placeholder="URL de l'image" required>
        <input type="submit" value="Ajouter">
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Titre</th>
            <th>Description</th>
            <th>Image</th>
            <th>Créé le</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row["id"] ?></td>
                <td><?= htmlspecialchars($row["title"]) ?></td>
                <td><?= htmlspecialchars($row["description"]) ?></td>
                <td><img src="<?= htmlspecialchars($row["image_url"]) ?>" alt="image projet"></td>
                <td><?= $row["created_at"] ?></td>
                <td><a href="project_edit.php?id=<?= $row['id'] ?>">Modifier</a></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <br><a href="index.php">← Retour à l'accueil</a>
</div>
</body>
</html>
