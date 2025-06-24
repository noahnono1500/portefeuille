<?php
session_start();
$conn = new mysqli("localhost", "root", "", "portfolio");
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

if (!isset($_SESSION["user_id"])) {
    die("Accès refusé.");
}

$user_id = $_SESSION["user_id"];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Projet invalide.");
}

$project_id = intval($_GET['id']);

// Vérifier que le projet appartient à l'utilisateur
$stmt = $conn->prepare("SELECT title, description, image_url FROM projects WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $project_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Projet non trouvé ou accès interdit.");
}

$project = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = htmlspecialchars($_POST["title"]);
    $description = htmlspecialchars($_POST["description"]);
    $image_url = htmlspecialchars($_POST["image_url"]);

    $stmt = $conn->prepare("UPDATE projects SET title = ?, description = ?, image_url = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sssii", $title, $description, $image_url, $project_id, $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: projects.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le projet</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="form-container">
    <h2>Modifier le projet</h2>
    <form method="POST">
        <input type="text" name="title" placeholder="Titre du projet" value="<?= htmlspecialchars($project['title']) ?>" required><br>
        <textarea name="description" placeholder="Description" required><?= htmlspecialchars($project['description']) ?></textarea><br>
        <input type="text" name="image_url" placeholder="URL de l'image" value="<?= htmlspecialchars($project['image_url']) ?>" required><br>
        <input type="submit" value="Mettre à jour">
    </form>
    <br><a href="projects.php">← Retour à mes projets</a>
</div>
</body>
</html>
