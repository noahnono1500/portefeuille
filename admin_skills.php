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
$stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    if ($row["role"] !== "admin") {
        die("Accès réservé à l'administrateur.");
    }
} else {
    die("Utilisateur non trouvé.");
}

// Traitement ajout compétence
if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST["name"])) {
    $name = htmlspecialchars($_POST["name"]);
    $sql = "INSERT INTO skills (name) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->close();
}

// Récupération des compétences
$skills = $conn->query("SELECT * FROM skills ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Admin - Gestion des compétences</title>
    <style>
        body {
            background-color: #111;
            color: #eee;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0; padding: 20px;
            min-height: 100vh;
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #fff;
        }
        form {
            background-color: #222;
            padding: 20px;
            border-radius: 10px;
            max-width: 400px;
            margin: 0 auto 30px auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.7);
            display: flex;
            flex-direction: column;
        }
        form input[type="text"] {
            padding: 12px;
            border-radius: 6px;
            border: none;
            font-size: 16px;
            margin-bottom: 15px;
            background-color: #333;
            color: #eee;
            box-sizing: border-box;
        }
        form input[type="submit"] {
            background-color: #28a745;
            color: #fff;
            border: none;
            font-weight: bold;
            padding: 12px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        form input[type="submit"]:hover {
            background-color: #1e7e34;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #222;
            box-shadow: 0 0 10px rgba(0,0,0,0.7);
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
            color: #28a745;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
        .btn-back {
            display: inline-block;
            margin-top: 20px;
            background: #444;
            padding: 10px 15px;
            border-radius: 8px;
            color: #eee;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        .btn-back:hover {
            background-color: #666;
        }
        @media (max-width: 600px) {
            form, table {
                width: 100%;
                overflow-x: auto;
            }
            th, td {
                padding: 10px 8px;
            }
        }
    </style>
</head>
<body>
    <h2>Gestion des compétences - Panneau Admin</h2>

    <form method="POST" action="admin_skills.php" novalidate>
        <input type="text" name="name" placeholder="Nouvelle compétence" required autocomplete="off">
        <input type="submit" value="Ajouter une compétence">
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom de la compétence</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($skill = $skills->fetch_assoc()): ?>
                <tr>
                    <td><?= $skill["id"] ?></td>
                    <td><?= htmlspecialchars($skill["name"]) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="admin.php" class="btn-back">← Retour au panneau d'administration</a>
</body>
</html>
