<?php
session_start() ;
// $_SESSION["wishlist"] === [] ;
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="index.css">
    <style>
        .none {
            text-decoration: none;
            background: linear-gradient(to right, #9340FF, #FF3C5F);
            margin: 5px;
            padding: 12px;
            border-radius: 25px;
        }

        .none:hover {
            background: linear-gradient(to left, #9340FF, #FF3C5F);
        }


        #divs div {
            width: 400px;
            height: 400px;
        }
        .hero-text {
            max-width: 800px;
            margin: 0 auto;
        }

    </style>

</head>

<body>

    <nav>
        <img src="media/homepage/nourim.png" alt="logo" class="navlogo">
        
            <?php
            // var_dump($_SESSION) ;
            if (isset($_SESSION["login"]) === true && $_SESSION["admin"] === true) {
                echo " <div>
                <div class='navbar-toggle' onclick='toggleNavbar()'>
                    <div></div>
                    <div></div>
                </div>
                <ul id='navbar-links'>
                    <li><a href='Orders.php' class='none' title='Commandes'>Commandes</a></li>
                    <li><a href='Logout.php' class='none' title='Déconnexion'>Déconnexion</a></li>
                </ul>
                </div>";

            } elseif (isset($_SESSION["login"]) === true) {
                echo "<ul><li><a href=''><img src='media/homepage/commande.png' alt='' width='25px' title='Commande'></a></li>";
                echo "<li><a href='profil.php'><img src='media/homepage/user.png' alt='' width='25px' title='Profil'></a></li>";
                echo "<li><a href=''><img src='media/homepage/wishlist.png' alt='' width='25px' title='Wishlist'></a></li>";
                echo "<li><a href=''><img src='media/homepage/shopping-cart.png' alt='' width='25px' title='Shopping Cart'></a></li>";
                echo "<li><a href='logout.php'><img src='media/homepage/logout.png' alt='' width='25px' title='Logout'></a></li></ul>";
            } else {
                echo "<ul><li><a href=''><img src='media/homepage/wishlist.png' alt='' width='25px' title='Wishlist'></a></li>";
                echo "<li><a href=''><img src='media/homepage/shopping-cart.png' alt='' width='25px' title='Shopping Cart'></a></li>";
                echo "<li><a href='loginpage.php'><img src='media/homepage/user.png' alt='' width='25px' title='Login'></a></li>";
                echo "<li><a href='registerpage.php'><img src='media/homepage/add-user.png' alt='' width='25px' title='Register'></a></li></ul>";
            }

            ?>
        
    </nav>

    <main>
        <section class="image-background">
            <div class="image-container">
                <img src="media/homepage/homepagebg.png" alt="Image de fond" width="100%" height="100%">
                <div class="image-overlay">
                <div class="hero-text overlay-text">
                <h1>Découvrez nos services d'impression de haute qualité</h1>
                <p>Offrez-vous des impressions personnalisées pour tous vos besoins</p>
                <button class="cta-button">En savoir plus</button>
            </div>

                </div>
            </div>
        </section>

        <section id="divs">
            <div>
                <h2>Services</h2>
                <img src="media/homepage/shopstore.jpg" alt="" class="divimg">
                <a href="services.php"><button >CLIQUEZ POUR LES SERVICES</button></a>
            </div>
            <div>
                <h2>Magasin de produits</h2>
                <img src="media/homepage/shopstore.jpg" alt="" class="divimg">
                <button onclick="">CLIQUEZ POUR LE MAGASIN DE PRODUITS</button>
            </div>

        </section>


    </main>

    <footer> &copy; Droits d'auteur de Nourim print </footer>


<!-- JavaScript for toggling the navbar -->
<?php
    // Check if the user is logged in and is an admin
    if (isset($_SESSION["login"]) && $_SESSION["admin"]) {
        echo '<script>
            function toggleNavbar() {
                var navbarLinks = document.getElementById("navbar-links");
                navbarLinks.classList.toggle("active");
            }
        </script>';
    }
    ?>

</body>

</html>