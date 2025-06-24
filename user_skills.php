<?php
session_start();
$conn = new mysqli("localhost", "root", "", "portfolio");
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

if (!isset($_SESSION["user_id"])) {
    die("Accès refusé. <a href='login.php'>Se connecter</a>");
}

$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["skills"]) && isset($_POST["levels"])) {
    // Supprimer les anciennes compétences
    $stmt = $conn->prepare("DELETE FROM user_skills WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // Insérer les nouvelles compétences
    $stmt = $conn->prepare("INSERT INTO user_skills (user_id, skill_id, level) VALUES (?, ?, ?)");
    foreach ($_POST["skills"] as $index => $skill_id) {
        $level = $_POST["levels"][$index];
        if (!empty($skill_id) && !empty($level)) {
            $stmt->bind_param("iis", $user_id, $skill_id, $level);
            $stmt->execute();
        }
    }
    $stmt->close();
}

$skills = $conn->query("SELECT * FROM skills ORDER BY name ASC");

$user_skills = [];
$stmt = $conn->prepare("SELECT skill_id, level FROM user_skills WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $user_skills[$row["skill_id"]] = $row["level"];
}
$stmt->close();

$levels = ["Débutant", "Intermédiaire", "Confirmé", "Expert"];
$level_percent = [
    "Débutant" => 25,
    "Intermédiaire" => 50,
    "Confirmé" => 75,
    "Expert" => 100,
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<title>Mes compétences</title>
<style>
    * {
        box-sizing: border-box;
    }
    body {
        margin: 0; padding: 20px; font-family: Arial, sans-serif; background: #121212; color: #eee;
        display: flex; justify-content: center;
    }
    .container {
        background: #222; padding: 20px; border-radius: 10px; width: 100%; max-width: 700px;
        box-shadow: 0 0 10px rgba(0,0,0,0.7);
    }
    h2 {
        text-align: center; color: #0f9;
        margin-bottom: 20px;
    }
    form {
        width: 100%;
    }
    .skill-pair {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        gap: 10px;
        flex-wrap: wrap;
    }
    select {
        flex-grow: 1;
        padding: 8px 12px;
        border-radius: 6px;
        border: none;
        font-size: 16px;
        background: #333;
        color: #eee;
    }
    button.remove-btn {
        background: #d33;
        border: none;
        color: white;
        padding: 8px 14px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 18px;
        line-height: 1;
        transition: background-color 0.3s ease;
    }
    button.remove-btn:hover {
        background: #a00;
    }
    button#addSkill {
        background: #0f9;
        border: none;
        color: #111;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 18px;
        cursor: pointer;
        margin-top: 15px;
        width: 100%;
        transition: background-color 0.3s ease;
    }
    button#addSkill:hover {
        background: #0c7;
    }
    input[type="submit"] {
        background: #09c;
        border: none;
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        font-size: 20px;
        cursor: pointer;
        margin-top: 25px;
        width: 100%;
        transition: background-color 0.3s ease;
    }
    input[type="submit"]:hover {
        background: #06a;
    }
    .progress-bar {
        flex-basis: 100%;
        height: 24px;
        background-color: #444;
        border-radius: 12px;
        overflow: hidden;
        margin-top: 6px;
        position: relative;
    }
    .progress-fill {
        height: 100%;
        color: #121;
        font-weight: bold;
        text-align: center;
        background-color: #0f9;
        line-height: 24px;
        transition: width 0.3s ease;
        white-space: nowrap;
    }
    a.back-link {
        display: block;
        text-align: center;
        margin-top: 25px;
        color: #0f9;
        text-decoration: none;
        font-weight: bold;
    }
    a.back-link:hover {
        text-decoration: underline;
    }
    @media(max-width: 600px) {
        .skill-pair {
            flex-direction: column;
            align-items: stretch;
        }
        button.remove-btn {
            width: 100%;
        }
    }
</style>
</head>
<body>
<div class="container">
    <h2>🎓 Mes compétences</h2>
    <form method="POST" id="skillsForm">
        <?php
        if (!empty($user_skills)) {
            foreach ($user_skills as $skill_id => $level) :
                $levelKey = ucfirst(strtolower(trim($level)));
                $width = $level_percent[$levelKey] ?? 0;
                $skills->data_seek(0);
                ?>
                <div class="skill-pair">
                    <select name="skills[]">
                        <option value="">-- Sélectionner une compétence --</option>
                        <?php
                        while ($skill = $skills->fetch_assoc()) {
                            $selected = ($skill['id'] == $skill_id) ? "selected" : "";
                            echo "<option value='{$skill['id']}' $selected>" . htmlspecialchars($skill['name']) . "</option>";
                        }
                        ?>
                    </select>
                    <select name="levels[]">
                        <option value="">-- Niveau --</option>
                        <?php
                        foreach ($levels as $lvl) {
                            $sel = ($lvl == $level) ? "selected" : "";
                            echo "<option value='$lvl' $sel>$lvl</option>";
                        }
                        ?>
                    </select>
                    <button type="button" class="remove-btn" onclick="removeSkill(this)">❌</button>
                    <div class="progress-bar" title="<?= htmlspecialchars($levelKey) ?>">
                        <div class="progress-fill" style="width: <?= $width ?>%"><?= htmlspecialchars($levelKey) ?></div>
                    </div>
                </div>
                <?php
            endforeach;
        } else {
            ?>
            <div class="skill-pair">
                <select name="skills[]">
                    <option value="">-- Sélectionner une compétence --</option>
                    <?php
                    while ($skill = $skills->fetch_assoc()) {
                        echo "<option value='{$skill['id']}'>" . htmlspecialchars($skill['name']) . "</option>";
                    }
                    ?>
                </select>
                <select name="levels[]">
                    <option value="">-- Niveau --</option>
                    <?php foreach ($levels as $lvl) {
                        echo "<option value='$lvl'>$lvl</option>";
                    } ?>
                </select>
                <button type="button" class="remove-btn" onclick="removeSkill(this)">❌</button>
                <div class="progress-bar" title="Aucun niveau">
                    <div class="progress-fill" style="width: 0%"></div>
                </div>
            </div>
            <?php
        }
        ?>
        <button type="button" id="addSkill">➕ Ajouter une compétence</button>
        <input type="submit" value="💾 Enregistrer mes compétences">
    </form>
    <a href="index.php" class="back-link">🏠 Retour à l'accueil</a>
</div>

<script>
    const skillsData = `<?php
        // Prépare les options pour select compétence à injecter dans JS
        $skills->data_seek(0);
        $options = "";
        while ($skill = $skills->fetch_assoc()) {
            $options .= "<option value='{$skill['id']}'>" . htmlspecialchars($skill['name']) . "</option>";
        }
        echo addslashes($options);
    ?>`;

    const levelsData = <?php echo json_encode($levels); ?>;

    document.getElementById('addSkill').addEventListener('click', () => {
        const container = document.getElementById('skillsForm');
        const div = document.createElement('div');
        div.classList.add('skill-pair');
        let levelsOptions = '<option value="">-- Niveau --</option>';
        levelsData.forEach(level => {
            levelsOptions += `<option value="${level}">${level}</option>`;
        });

        div.innerHTML = `
            <select name="skills[]">
                <option value="">-- Sélectionner une compétence --</option>
                ${skillsData}
            </select>
            <select name="levels[]">
                ${levelsOptions}
            </select>
            <button type="button" class="remove-btn" onclick="removeSkill(this)">❌</button>
            <div class="progress-bar" title="Aucun niveau">
                <div class="progress-fill" style="width: 0%"></div>
            </div>
        `;
        container.insertBefore(div, document.getElementById('addSkill'));
    });

    function removeSkill(btn) {
        const container = document.getElementById('skillsForm');
        if (container.querySelectorAll('.skill-pair').length > 1) {
            btn.parentElement.remove();
        } else {
            alert("Vous devez avoir au moins une compétence.");
        }
    }
</script>
</body>
</html>
