<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    die("Accès refusé. <a href='login.php'>Se connecter</a>");
}
if (!isset($_GET["id"])) {
    die("ID utilisateur manquant.");
}
$delete_id = intval($_GET["id"]);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation de suppression</title>
    <style>
        body {
            background: #121212;
            color: #eee;
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
        }
        .box {
            background: #222;
            padding: 30px;
            border-radius: 10px;
            max-width: 400px;
            margin: auto;
            box-shadow: 0 0 15px rgba(0,255,0,0.3);
        }
        a, button {
            background: #7ddb4e;
            color: #000;
            padding: 10px 20px;
            margin: 10px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }
        button:hover, a:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="box">
        <h2>Confirmer la suppression</h2>
        <p>Es-tu sûr de vouloir désactiver cet utilisateur ?</p>
        <form action="delete_user.php" method="get">
            <input type="hidden" name="id" value="<?= $delete_id ?>">
            <button type="submit">Oui, désactiver</button>
            <a href="admin_users.php">Annuler</a>
        </form>
    </div>
</body>
</html>
