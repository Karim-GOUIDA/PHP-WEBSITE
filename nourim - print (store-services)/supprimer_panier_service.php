<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["login"])) {
    header("Location: loginpage.php?m=1");
    exit;
}

// Vérifier si la méthode de requête est POST et si le bouton de suppression a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['supprimer_service'])) {
    // Inclure le fichier de connexion à la base de données
    include("connect.php");

    // Récupérer l'ID du service à supprimer
    $cart_id = $_POST['cart_id'];

    // Supprimer le service du panier
    $stmt = $con->prepare("DELETE FROM panier_services WHERE cart_id = ?");
    $stmt->execute([$cart_id]);

    // Rediriger vers la page du panier après la suppression
    header("Location: panier.php");
    exit;
} else {
    // Redirection si la méthode de requête n'est pas POST ou si le bouton de suppression n'a pas été soumis
    header("Location: error.php");
    exit;
}
?>
