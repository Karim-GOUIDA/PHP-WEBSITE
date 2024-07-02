<?php
session_start();

// Vérifier si l'utilisateur est connecté et est un admin
if (!isset($_SESSION["login"]) || !isset($_SESSION["admin"]) || $_SESSION["admin"] !== true) {
    header("Location: loginpage.php");
    exit;
}

// Inclure le fichier de connexion à la base de données
include("connect.php");

// Récupérer l'ID de la commande à afficher les informations supplémentaires
if (isset($_GET['id_cmd'])) {
    $id_commande = $_GET['id_cmd'];

    // Récupérer les informations de la commande depuis la base de données
    $stmt = $con->prepare("SELECT * FROM commandes WHERE id_commande = ?");
    $stmt->execute([$id_commande]);
    $commande = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$commande) {
        // Rediriger si la commande n'existe pas
        header("Location: commande.php");
        exit;
    }

    // Récupérer les articles associés à cette commande depuis la table commande_items
    $stmt_articles = $con->prepare("SELECT ci.*, p.nom_produit AS nom_produit_commande, s.nom_service AS nom_service_commande
                                    FROM commande_items ci
                                    LEFT JOIN produits p ON ci.article_id_ref = p.product_id AND ci.type_article = 'produit'
                                    LEFT JOIN services s ON ci.article_id_ref = s.service_id AND ci.type_article = 'service'
                                    WHERE ci.id_commande = ?");
    $stmt_articles->execute([$id_commande]);
    $articles = $stmt_articles->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer le client infos
    // Récupérer le client infos
    $stmt_user = $con->prepare("SELECT * FROM utilisateurs WHERE user_id = ?");
    $stmt_user->execute([$_SESSION["id"]]); 
    $user = $stmt_user->fetch(PDO::FETCH_ASSOC); 


    


} else {
    // Rediriger si l'ID de la commande n'est pas spécifié
    header("Location: commande.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Commande</title>
    <link rel="stylesheet" href="commande_info.css">
</head>
<body>

    <nav>
        <a href='index.php'><img src='media/homepage/nourim.png' alt='logo' class='navlogo'></a>
        <div>
            <div class='navbar-toggle' onclick='toggleNavbar()'>
                <div></div>
                <div></div>
                <div></div>
            </div>
            <ul id='navbar-links'>
            <li><a href='index.php' class='none' title='Accueil'>Accueil</a></li>
            <li><a href='commande.php' class='none' title='Commandes'>Commandes</a></li>
            <li><a href='Logout.php' class='none' title='Déconnexion'>Déconnexion</a></li>
       </ul>
        </div>
    </nav>




    <div class="m">
    <div class="container">
        <h1>Détails de la Commande</h1>
        <p><strong>ID Commande:</strong> <?= $commande['id_commande'] ?></p>
        <p><strong>ID Utilisateur:</strong> <?= $commande['id_user'] ?> </p>
        <p><strong>Utilisateur username :</strong> <?= $user['username']; ?></p>
        <p><strong>Numero Telephone :</strong> <?= $user['numero_telephone']; ?></p>
        <p><strong>Adresse :</strong> <?= $user['adresse']; ?></p>
        <p><strong>Date Commande:</strong> <?= $commande['date_commande'] ?></p>
        <p><strong>Montant Total:</strong> <?= $commande['montant_total'] ?> Dh</p>
        <p><strong>Statut:</strong> <?= $commande['statut_commande'] ?></p>

        <?php if (count($articles) > 0) : ?>
            <h2>Articles Commandés</h2>
            <ul>
                <?php foreach ($articles as $article) : ?>
                    <li>
                        <strong>ID Article:</strong> <?= $article['item_id'] ?><br>
                        <?php if ($article['type_article'] == 'produit') : ?>
                            <strong>Nom du Produit:</strong> <?= $article['nom_produit_commande'] ?><br>
                        <?php elseif ($article['type_article'] == 'service') : ?>
                            <strong>Nom du Service:</strong> <?= $article['nom_service_commande'] ?><br>
                        <?php endif; ?>
                        <strong>Quantité:</strong> <?= $article['quantite'] ?><br>
                        <strong>Prix Unitaire:</strong> <?= $article['prix_unitaire'] ?> Dh<br>
                        
                        
                    </li>

                    

                <?php endforeach; ?>
                <?php var_dump($_SESSION); ?>
            </ul>

            <form method="post" action="update_commande.php">
                <input type="hidden" name="id_commande" value="<?= $commande['id_commande'] ?>">
                <button type="submit" name="enattente">Changer en attente</button>
                <button type="submit" name="encours">Changer en cours</button>
                <button type="submit" name="terminee">Changer terminée</button>
                
            </form>    


        <?php else : ?>
            <p>Aucun article associé à cette commande.</p>
        <?php endif; ?>

        <a href="commande.php" class="btn-back">Retour à la liste des commandes</a>
    </div>
    </div>


    
    <footer> &copy; Droits d'auteur de Nourim print </footer>



</body>

<script>
    function toggleNavbar() {
        var navbarLinks = document.getElementById("navbar-links");
        navbarLinks.classList.toggle("active");
    }
</script>


</html>
