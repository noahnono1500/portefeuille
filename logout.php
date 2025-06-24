<?php
session_start();
session_destroy();

if (isset($_COOKIE["user_id"])) {
    setcookie("user_id", "", time() - 3600, "/");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Déconnexion</title>
    <style>
        body {
            background-color: #111;
            color: #eee;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            text-align: center;
        }
        .message-box {
            background-color: #222;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.7);
        }
        a {
            display: inline-block;
            margin-top: 20px;
            color: #17a2b8;
            font-weight: 600;
            text-decoration: none;
            font-size: 16px;
            padding: 10px 25px;
            border: 2px solid #17a2b8;
            border-radius: 8px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        a:hover {
            background-color: #17a2b8;
            color: #111;
        }
    </style>
</head>
<body>
    <div class="message-box">
        <h1>Vous êtes déconnecté</h1>
        <p>Merci d'être passé !</p>
        <a href="login.php">Se reconnecter</a>
    </div>
</body>
</html>
