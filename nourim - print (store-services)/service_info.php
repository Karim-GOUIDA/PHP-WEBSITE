<?php
session_start();

if (!isset($_SESSION["login"]) || isset($_SESSION['admin']) && $_SESSION['admin'] === true ) {
    header("Location: loginpage.php?m=1");
    exit;
}

if (isset($_GET['id'])) {
    include("connect.php");
    $service_id = $_GET['id'];
    $stmt = $con->prepare("SELECT * FROM services WHERE service_id = ?");
    $stmt->execute([$service_id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);
    
} else {
    header("Location: error.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
    // Récupérer les données du formulaire
    $quantite = $_POST['quantite'];


    echo"<script>
    alert (".$_POST['quantite'].") ;
</script>";

    

    // Insérer ces données dans la table panier :
    $stmt = $con->prepare("INSERT INTO panier_services (user_id, service_id, quantite) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['id'], $service_id, $quantite]);

    // Rediriger vers la page du panier ou une autre page
    header("Location: panier.php");
    exit;
}





?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Infos sur le service</title>
    <link rel="stylesheet" href="service_info.css">
</head>

<body>


    <?php
    // BARRE DE NAVIGATION
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

    // CONTENU PRINCIPAL
    echo "<div class='main'>";
    if ($service) {
        echo "<section class='service'>";
        echo "<img src='" . $service['photo'] . "' alt='" . $service['nom_service'] . "'>";
        echo "<div class='det'>";
        echo "<h1 class='m' >" . $service['nom_service'] . "</h1>";
        echo "<p class='m' >Prix: " . $service['prix'] . " Dh</p>";
        echo "<p class='m' >Description: " . $service['description'] . "</p>";
        echo "<form method='POST'>";
        echo "<label for='quantite'>Quantité</label><br>";
        echo "<select class='select-style' name='quantite'>";
        echo "<option value='1'>100</option>";
        echo "<option value='5'>500</option>";
        echo "<option value='10'>1000</option>";
        echo "</select>";
        echo "<div>";
        echo "<button class='btn m-r' type='submit' name='add_to_cart'>Ajouter au panier</button>";
        echo "<button class='btn' type='submit' name='checkout'>Commander maintenant</button>";
        echo "</div>";
        echo "</form>";
        // if ($service['disponibilite'] === 1) {
        //     echo "<p class='green'>Disponible</p>";
        // } else {
        //     echo "<p class='red'>Indisponible</p>";
        // }
        echo "</div>";
        echo "</section>";
    } else {
        echo "Le service demandé n'existe pas.";
    }
    echo "</div>";
    ?>



<footer> &copy; Droits d'auteur de Nourim print </footer>


    <script>
        function toggleNavbar() {
            var navbarLinks = document.getElementById("navbar-links");
            navbarLinks.classList.toggle("active");
        }
    </script>
</body>

</html>
