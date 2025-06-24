<?php
session_start();
$conn = new mysqli("localhost", "root", "", "portfolio");
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Connexion par cookie si session absente
if (!isset($_SESSION["user_id"]) && isset($_COOKIE["user_id"])) {
    $_SESSION["user_id"] = $_COOKIE["user_id"];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars($_POST["email"]);
    $password = $_POST["password"];
    $remember = isset($_POST["remember"]);

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION["user_id"] = $id;

            if ($remember) {
                setcookie("user_id", $id, time() + (86400 * 30), "/"); // 30 jours
            }

            header("Location: index.php");
            exit;
        } else {
            $error = "Mot de passe incorrect.";
        }
    } else {
        $error = "Utilisateur non trouvé.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Connexion</title>
    <style>
        body {
          background-color: #000;
          font-family: Arial, sans-serif;
          color: #eee;
          margin: 0;
          padding: 50px;
          display: flex;
          justify-content: center;
          align-items: center;
          min-height: 100vh;
        }

        form {
          background-color: #121212;
          padding: 30px 40px;
          border-radius: 10px;
          box-shadow: 0 0 20px #4caf50;
          width: 320px;
          text-align: center;
        }

        form h2 {
          color: #66bb6a;
          margin-bottom: 25px;
          border-bottom: 2px solid #66bb6a;
          padding-bottom: 10px;
        }

        input[type="email"],
        input[type="password"] {
          width: 100%;
          padding: 10px 15px;
          margin: 10px 0 20px;
          background-color: #000;
          border: 1.5px solid #66bb6a;
          border-radius: 6px;
          color: #eee;
          font-size: 16px;
          transition: border-color 0.3s ease;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
          outline: none;
          border-color: #a6d785;
        }

        label {
          font-size: 14px;
          color: #a0d27a;
          user-select: none;
        }

        input[type="checkbox"] {
          margin-right: 8px;
          vertical-align: middle;
        }

        input[type="submit"] {
          background-color: #66bb6a;
          border: none;
          padding: 12px;
          width: 100%;
          border-radius: 8px;
          font-size: 18px;
          font-weight: bold;
          cursor: pointer;
          color: #000;
          transition: background-color 0.3s ease;
          margin-top: 20px;
        }

        input[type="submit"]:hover {
          background-color: #a6d785;
        }

        p {
          margin-top: 20px;
        }

        a {
          color: #66bb6a;
          text-decoration: none;
          font-size: 14px;
        }

        a:hover {
          text-decoration: underline;
        }

        .error {
          color: #ff4c4c;
          margin-top: 15px;
          font-weight: bold;
        }
    </style>
</head>
<body>
    <form method="POST" action="login.php">
        <h2>Connexion</h2>
        <input type="email" name="email" placeholder="Adresse email" required><br />
        <input type="password" name="password" placeholder="Mot de passe" required><br />
        <label><input type="checkbox" name="remember"> Se souvenir de moi</label><br />
        <input type="submit" value="Se connecter"><br />
        <p><a href="reset_password.php">Mot de passe oublié ?</a></p>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    </form>
</body>
</html>