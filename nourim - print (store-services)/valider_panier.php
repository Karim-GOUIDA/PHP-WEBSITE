<?php
session_start();

if (!isset($_SESSION["login"])) {
    header("Location: loginpage.php?m=1");
    exit;
}

// Inclure le fichier de connexion à la base de données
include("connect.php");

// Récupérer l'ID de l'utilisateur connecté
$user_id = $_SESSION['id'];

// Fonction pour récupérer les produits du panier
function getPanierProduits($user_id)
{
    global $con;
    $stmt = $con->prepare("SELECT * FROM panier_produits ps INNER JOIN produits p ON ps.product_id = p.product_id  WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour récupérer les services du panier
function getPanierServices($user_id)
{
    global $con;
    $stmt = $con->prepare("SELECT * FROM panier_services ps INNER JOIN services s ON ps.service_id = s.service_id WHERE ps.user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Récupérer les produits du panier
$panier_produits = getPanierProduits($user_id);

// Récupérer les services du panier
$panier_services = getPanierServices($user_id);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['valider_panier'])) {
    try {
        $con->beginTransaction();

        // Calculer le montant total de la commande
        $montant_total = 0;
        foreach ($panier_produits as $produit) {
            $montant_total += $produit['prix'] * $produit['quantite'];
        }

        foreach ($panier_services as $service) {
            $montant_total += $service['prix'] * $service['quantite'];
        }

        // Insérer la commande dans la table commandes
        $date_commande = date("Y-m-d");
        $stmt_commande = $con->prepare("INSERT INTO commandes (id_user, date_commande, montant_total, statut_commande) VALUES (?, ?, ?, 'en attente')");
        $stmt_commande->execute([$user_id, $date_commande, $montant_total]);

        // Récupérer l'ID de la commande insérée
        $id_commande = $con->lastInsertId();

        // Insérer les articles du panier dans la table commande_items
        foreach ($panier_produits as $produit) {
            $stmt_produit = $con->prepare("INSERT INTO commande_items (id_commande, type_article, article_id_ref, quantite, prix_unitaire) VALUES (?, 'produit', ?, ?, ?)");
            $stmt_produit->execute([$id_commande, $produit['product_id'], $produit['quantite'], $produit['prix']]);
        }

        foreach ($panier_services as $service) {
            $stmt_service = $con->prepare("INSERT INTO commande_items (id_commande, type_article, article_id_ref, quantite, prix_unitaire) VALUES (?, 'service', ?, ?, ?)");
            $stmt_service->execute([$id_commande, $service['service_id'], $service['quantite'], $service['prix']]);
        }

        // Vider le panier (supprimer les produits et services du panier)
        $stmt_supprimer_produits = $con->prepare("DELETE FROM panier_produits WHERE user_id = ?");
        $stmt_supprimer_produits->execute([$user_id]);

        $stmt_supprimer_services = $con->prepare("DELETE FROM panier_services WHERE user_id = ?");
        $stmt_supprimer_services->execute([$user_id]);

        // Valider la transaction
        $con->commit();

        // Redirection vers la page de confirmation de commande avec un message
        header("Location: confirmation_commande.php?commande_id=$id_commande");
        exit;
    } catch (Exception $e) {
        // En cas d'erreur, annuler la transaction et afficher un message d'erreur
        $con->rollBack();
        echo "Erreur lors de la validation de la commande : " . $e->getMessage();
    }
}
?>
