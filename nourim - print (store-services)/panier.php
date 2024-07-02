<?php
session_start();


// var_dump($_SESSION) ;


if (!isset($_SESSION["login"])) {
    header("Location: loginpage.php?m=1");
    exit;
}

// Inclure le fichier de connexion à la base de données
include("connect.php");

// Fonction pour récupérer les produits du panier
function getPanierProduits($user_id)
{
    global $con;
    $stmt = $con->prepare("SELECT * FROM panier_produits ps INNER JOIN produits p ON ps.product_id = p.product_id  WHERE ps.user_id = ?");
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


// Récupérer l'ID de l'utilisateur connecté
$user_id = $_SESSION['id'];

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
        header("Location: commande.php");
        exit;
    } catch (Exception $e) {
        // En cas d'erreur, annuler la transaction et afficher un message d'erreur
        $con->rollBack();
        echo "Erreur lors de la validation de la commande : " . $e->getMessage();
    }
}
$con = null; // Fermer la connexion à la base de données

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier</title>
    <link rel="stylesheet" href="panier.css">
</head>

<body>

     <?php
        echo "<nav>
            <a href='index.php'><img src='media/homepage/nourim.png' alt='logo' class='navlogo'></a>
            <div>
            <div class='navbar-toggle' onclick='toggleNavbar()'>
                <div></div>
                <div></div>
                <div></div>
            </div>
            <ul id='navbar-links'> ";
        echo "
            <li><a href='index.php'><img src='media/homepage/home.png' alt='' width='25px' title='Home'></a></li>
            <li><a href='commande.php'><img src='media/homepage/commande.png' alt='' width='25px' title='Commande'></a></li>";
        echo "<li><a href='profil.php'><img src='media/homepage/user.png' alt='' width='25px' title='Profil'></a></li>";
        echo "<li><a href='panier.php'><img src='media/homepage/shopping-cart.png' alt='' width='25px' title='Shopping Cart'></a></li>";
        echo "<li><a href='logout.php'><img src='media/homepage/logout.png' alt='' width='25px' title='Logout'></a></li>";
        echo "</ul>
            </div>
            </nav>
            ";
    ?> 

    <div class="main">
        <h1>Votre panier</h1>

        <h2>Produits</h2>

        <?php
        if ($panier_produits) {
        ?>
            <ul class="panier-list">
                <?php foreach ($panier_produits as $produit) : ?>
                    <li class="panier-item">
                        <div class="panier-item-details">
                            <img src="<?= $produit['photo'] ?>" alt="<?= $produit['nom_produit'] ?>" class="panier-item-image">
                            <div class="panier-item-info">
                                <p class="panier-item-name"><?= $produit['nom_produit'] ?></p>
                                <p class="panier-item-price">Prix: <?= $produit['prix'] ?> Dh pour 1 pièce</p>
                                <p class="panier-item-quantite">Quantité: <?= $produit['quantite'] ?></p>
                            </div>
                        </div>
                        <form method="POST" action="supprimer_panier_produit.php">
                            <input type="hidden" name="cart_id" value="<?= $produit['cart_id'] ?>">
                            <button type="submit" class="supprimer-btn" name="supprimer_produit">Supprimer</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php
        } else {
            echo "<p class='empty-panier-message' >Aucun produit dans le panier.</p>";
        }
        ?>


        <h2>Services</h2>

        <?php
        if ($panier_services) {
        ?>
            <ul class="panier-list">
                <?php foreach ($panier_services as $service) : ?>
                    <li class="panier-item">
                        <div class="panier-item-details">
                            <img src="<?= $service['photo'] ?>" alt="<?= $service['nom_service'] ?>" class="panier-item-image">
                            <div class="panier-item-info">
                                <p class="panier-item-name"><?= $service['nom_service'] ?></p>
                                <p class="panier-item-price">Prix: <?= $service['prix'] ?> Dh</p>
                                <p class="panier-item-quantite">Quantité: <?= $service['quantite'] ?></p>
                            </div>
                        </div>
                        <form method="POST" action="supprimer_panier_service.php">
                            <input type="hidden" name="cart_id" value="<?= $service['cart_id'] ?>">
                            <button type="submit" class="supprimer-btn" name="supprimer_service">Supprimer</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php
        } else {
            echo "<p class='empty-panier-message' >Aucun service dans le panier.</p>";
        }
        ?>

    <?php
        if($panier_services || $panier_produits){
            echo "
            <form method='POST' action='' class='valider-panier-form'>
                <button type='submit' class='valider-btn' name='valider_panier'>Valider le panier</button>
            </form>
            ";
        }
    ?>

    


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