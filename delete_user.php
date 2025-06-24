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

// Supprimer l'utilisateur si ID valide et différent de l'admin courant
if (isset($_GET["id"])) {
    $delete_id = intval($_GET["id"]);
    if ($delete_id === $user_id) {
        die("Vous ne pouvez pas supprimer votre propre compte administrateur.");
    }

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        header("Location: admin_users.php");
        exit;
    } else {
        echo "Erreur lors de la suppression.";
    }
    $stmt->close();
} else {
    echo "ID utilisateur manquant.";
}
?>
