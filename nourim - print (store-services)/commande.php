<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["login"])) {
    header("Location: loginpage.php?m=1");
    exit;
}

// Include the database connection file
include("connect.php");

// Retrieve the user ID from the session
$user_id = $_SESSION["id"];

// Fetch all commandes for the user from the database
$stmt = $con->prepare("SELECT * FROM commandes WHERE id_user = ?");
$stmt->execute([$user_id]);
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);


$stmt = $con->prepare("SELECT * FROM commandes");
$stmt->execute(); // Execute the prepared statement
$allcommandes = $stmt->fetchAll(PDO::FETCH_ASSOC);




$con = null; // Close the database connection
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Commandes</title>
    <link rel="stylesheet" href="commande.css">
</head>

<body>

    <?php


    if ($_SESSION["admin"] === true) {

        // Admin view

        echo "<nav>
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
        ";
    ?>

        <div class="m">
            <div class="containerui">
                <h1>Liste des Commandes</h1>

                <?php if (count($allcommandes) > 0) : ?>
                    <ul>
                        <?php foreach ($allcommandes as $commande) : ?>
                            <li>
                                <strong>ID Commande:</strong> <?= $commande['id_commande'] ?><br>
                                <strong>Date Commande:</strong> <?= $commande['date_commande'] ?><br>
                                <strong>Montant Total:</strong> <?= $commande['montant_total'] ?> Dh<br>
                                <strong>Statut:</strong> <span class="green" ><?= $commande['statut_commande'] ?></span><br>
                                <form method="post" action="update_commande.php">
                                    <input type="hidden" name="id_commande" value="<?= $commande['id_commande'] ?>">
                                    <button type="submit" name="enattente">Changer en attente</button>
                                    <button type="submit" name="encours">Changer en cours</button>
                                    <button type="submit" name="terminee">Changer terminée</button>
                                    <button type="submit" name="plusinfo">Plus d'informations</button>
                                    <!-- <button>Supprimer Commande</button> -->
                                </form>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <p>Aucune commande trouvée.</p>
                <?php endif; ?>

            </div>
        </div>
    <?php
    } else {


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

        <h1>Liste des Commandes</h1>


        <div>
            <div class="containerui">

        <?php if (count($commandes) > 0) : ?>
            <ul>
                <?php foreach ($commandes as $commande) : ?>
                    <li>
                        <strong>ID Commande:</strong> <?= $commande['id_commande'] ?><br>
                        <strong>Date Commande:</strong> <?= $commande['date_commande'] ?><br>
                        <strong>Montant Total:</strong> <?= $commande['montant_total'] ?> Dh<br>
                        <strong>Statut:</strong> <?= $commande['statut_commande'] ?><br>
                        <!-- You can add more details here as needed -->
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <p>Aucune commande trouvée pour cet utilisateur.</p>
        <?php endif;
        }
        ?>

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