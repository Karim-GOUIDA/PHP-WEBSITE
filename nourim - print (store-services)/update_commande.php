<?php

include "connect.php" ;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check which button was clicked
    if (isset($_POST['encours'])) {
        // Update the status to "en cours" for the corresponding commande
        $id_commande = $_POST['id_commande'];
        $stmt = $con->prepare("UPDATE commandes SET statut_commande = 'en cours' WHERE id_commande = ?");
        $stmt->execute([$id_commande]);
        // Redirect back to the list of commandes
        header("Location: commande.php");
        exit;
    }elseif (isset($_POST['enattente'])) {
        // Update the status to "terminée" for the corresponding commande
        $id_commande = $_POST['id_commande'];
        $stmt = $con->prepare("UPDATE commandes SET statut_commande = 'en attente' WHERE id_commande = ?");
        $stmt->execute([$id_commande]);
        // Redirect back to the list of commandes
        header("Location: commande.php");
        exit;
    } elseif (isset($_POST['terminee'])) {
        // Update the status to "terminée" for the corresponding commande
        $id_commande = $_POST['id_commande'];
        $stmt = $con->prepare("UPDATE commandes SET statut_commande = 'terminée' WHERE id_commande = ?");
        $stmt->execute([$id_commande]);
        // Redirect back to the list of commandes
        header("Location: commande.php");
        exit;
    }elseif (isset($_POST['plusinfo'])){
        // Update the status to "terminée" for the corresponding commande
        $id_commande = $_POST['id_commande'];

        header("Location: commande_info.php?id_cmd=".$id_commande);
        exit;
    }
}else{
    header("Location: index.php");
}
?>