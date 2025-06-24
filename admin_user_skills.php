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

// Requête pour récupérer les compétences des utilisateurs
$sql = "SELECT u.id AS user_id, u.email, s.name AS skill_name, us.level
        FROM user_skills us
        JOIN users u ON us.user_id = u.id
        JOIN skills s ON us.skill_id = s.id
        ORDER BY u.email, s.name";
$result = $conn->query($sql);

// Organisation des données par utilisateur
$userSkills = [];
while ($row = $result->fetch_assoc()) {
    $userSkills[$row['email']][] = [
        'skill' => $row['skill_name'],
        'level' => $row['level']
    ];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Admin - Compétences par utilisateur</title>
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
            margin-bottom: 30px;
            color: #fff;
        }
        .user-block {
            background-color: #222;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 0 12px rgba(0,0,0,0.7);
        }
        .user-email {
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 12px;
            color: #1abc9c;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #333;
            box-shadow: 0 0 10px rgba(0,0,0,0.6);
        }
        th, td {
            padding: 10px 14px;
            border-bottom: 1px solid #444;
            text-align: left;
            color: #eee;
        }
        th {
            background-color: #444;
        }
        tr:hover {
            background-color: #3a3a3a;
        }
        .progress-bar {
            height: 18px;
            background-color: #555;
            border-radius: 9px;
            overflow: hidden;
            width: 100%;
        }
        .progress-fill {
            height: 100%;
            color: #fff;
            font-size: 13px;
            line-height: 18px;
            text-align: center;
            white-space: nowrap;
        }
        /* Couleurs selon niveau */
        .Débutant { background-color: #3498db; }
        .Intermédiaire { background-color: #f1c40f; }
        .Confirmé { background-color: #e67e22; }
        .Expert { background-color: #e74c3c; }

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

        @media (max-width: 700px) {
            body {
                padding: 10px;
            }
            .user-block {
                padding: 10px 15px;
            }
            th, td {
                padding: 8px 10px;
                font-size: 14px;
            }
            .user-email {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <h2>Compétences par utilisateur - Panneau Admin</h2>

    <?php if (empty($userSkills)): ?>
        <p>Aucun utilisateur avec compétences enregistrées.</p>
    <?php else: ?>
        <?php foreach ($userSkills as $email => $skills): ?>
            <div class="user-block">
                <div class="user-email"><?= htmlspecialchars($email) ?></div>
                <table>
                    <thead>
                        <tr>
                            <th>Compétence</th>
                            <th>Niveau</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($skills as $skill): ?>
                            <?php
                                // Définition % barre selon niveau
                                $levels_percent = [
                                    "Débutant" => 25,
                                    "Intermédiaire" => 50,
                                    "Confirmé" => 75,
                                    "Expert" => 100
                                ];
                                $percent = $levels_percent[$skill['level']] ?? 0;
                                $class_level = htmlspecialchars($skill['level']);
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($skill['skill']) ?></td>
                                <td>
                                    <div class="progress-bar" title="<?= htmlspecialchars($skill['level']) ?>">
                                        <div class="progress-fill <?= $class_level ?>" style="width: <?= $percent ?>%;">
                                            <?= htmlspecialchars($skill['level']) ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <a href="admin.php" class="btn-back">← Retour au panneau d'administration</a>
</body>
</html>
