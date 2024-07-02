<?php
session_start();
include("Connect.php");

// Fetch products data from the database
$sql_fetch_products = "SELECT * FROM produits";
$stmt_fetch_products = $con->prepare($sql_fetch_products);
$stmt_fetch_products->execute();
$products = $stmt_fetch_products->fetchAll(PDO::FETCH_ASSOC);

// var_dump($_SESSION);

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get form data
    $user_id = $_SESSION["id"];
    $nom_produit = $_POST['nomproduit'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];

    // Check if a file is uploaded
    if (isset($_FILES['photo'])) {
        $file = $_FILES['photo'];
        $fileType = exif_imagetype($file['tmp_name']);
        $allowedTypes = array(IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF);

        // Check if the uploaded file is an image
        if (in_array($fileType, $allowedTypes)) {
            // Move the uploaded file to a permanent location
            $targetDir = "uploads/";
            $targetFile = $targetDir . basename($file['name']);
            move_uploaded_file($file['tmp_name'], $targetFile);

            // Save the file path and other form data to the database
            $photo = $targetFile;

            // Assuming you have a database connection established ($con)
            $stmt_insert_produit = $con->prepare("INSERT INTO produits (user_id, nom_produit, description, prix, photo) VALUES (?, ?, ?, ?, ?)");
            $stmt_insert_produit->execute([$user_id, $nom_produit, $description, $prix, $photo]);
            $message = "Le produit a été ajouté avec succès.";

            // Optionally, 
            // header("Location: produits.php");
            // exit;
        } else {
            echo "Seuls les fichiers JPEG, PNG et GIF sont autorisés.";
        }
    } else {
        echo "Aucun fichier téléchargé.";
    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produits</title>
    <link rel="stylesheet" href="produits.css">
</head>

<body>
    <!-- Navigation bar -->
    <?php

    var_dump($_SESSION) ;

    if (isset($_SESSION["admin"]) && $_SESSION["admin"] === true) {

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

        echo "<main>";

        echo "<h1> Ajouter produit </h1>";



        if (isset($message)) {
            echo "<p class='success-message'> $message</p>";
        }


        // Formulaire pour ajouter des produits

        echo "<div class='form-container'>
            <form method='POST' class='form' enctype='multipart/form-data'>
                <div class='form-group'>
                    <label for='nom-produit'>Nom du produit</label>
                    <input type='text' name='nomproduit' required>
                </div>
                <div class='form-group'>
                    <label for='description-produit'>Description du produit</label>
                    <textarea name='description' required></textarea>
                </div>
                <div class='form-group'>
                    <label for='prix'>Prix</label>
                    <input type='number' name='prix'  required>
                </div>
                <div class='form-group'>
                    <label for='photo'>Photo</label>
                    <input type='file' name='photo' required>
                </div>
                <div class='form-group'>
                    <button type='submit'>Ajouter produit</button>
                </div>
            </form>
        </div>
        ";

        echo "<div>";

        echo "<h1>produits </h1>";

        foreach ($products as $s) {
            echo "<section class='produit-section'>";
            echo "<img src='" . $s['photo'] . "' alt='" . $s['nom_produit'] . "' class='produit-image'>";
            echo "<div class='produit-details'>";
            echo "<h2 class='produit-title'>" . $s['nom_produit'] . "</h2>";
            echo "<p class='produit-description'>" . $s['description'] . "</p>";
            echo "<p class='produit-price'>Prix : " . $s['prix'] . " Dh</p>";
            echo "<div class='produit-actions'>";
            // edit
            
            echo "<a href='produit_edit.php?id=" . $s['product_id'] . "'><button class='btn-edit'>Modifier</button></a>";
            
            echo '<form method="POST" action="produit_delete.php">';
            echo '<input type="hidden" name="produit_id" value="' . $s['product_id'] . '">';
            echo '<button type="submit" name="delete" class="btn-delete">Supprimer</button>';
            echo '</form>';


            echo "</div>"; // Close produit-actions
            echo "</div>"; // Close produit-details
            echo "</section>";
        }

        echo "</div>";

        echo "</main>";





    } elseif ($_SESSION["login"] === true) {
       
    
        // users with account ;

        echo "<nav>
        <a href='index.php'><img src='media/homepage/nourim.png' alt='logo' class='navlogo'></a>
        <div>
        <div class='navbar-toggle' onclick='toggleNavbar()'>
            <div></div>
            <div></div>
            <div></div>
        </div>
        <ul id='navbar-links'> " ;
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


        echo "<div class='all-produits'>";

        if ($products){
            foreach ($products as $s) {

                echo "
                <div class='produit'>
                    <img src='" . $s['photo'] . "' alt='" . $s['nom_produit'] . "'>
                    <div class='produit-info'>
                        <h4 class='produit-title'>" . $s['nom_produit'] . "</h4>
                        <p class='produit-price'>" . $s['prix'] . " Dh </p>
                        ";
                // echo '<form method="POST" action="produit_delete.php">';
                // echo '<input type="hidden" name="produit_id" value="' . $s['produit_id'] . '">';
                // echo '<button type="submit" name="delete" class="btn-buy">Buy now</button>';
                // echo '</form>';
                echo "<a href='produit_info.php?id=" . $s['product_id'] . "'>Buy now</a>";
                echo "</div>
                    </div>";
                    
            }
        }else{
            echo "<h1>no produits dans le store</h1>" ;
        }
        
        echo "</div >";

    
    
    } else {
        // User without account

        echo "<nav>
        <a href='index.php'><img src='media/homepage/nourim.png' alt='logo' class='navlogo'></a>
        <div>
        <div class='navbar-toggle' onclick='toggleNavbar()'>
            <div></div>
            <div></div>
            <div></div>
        </div>
        <ul id='navbar-links'> " ;
        echo "<li><a href='loginpage.php'><img src='media/homepage/user.png' alt='' width='25px' title='Login'></a></li>";
        echo "<li><a href='registerpage.php'><img src='media/homepage/add-user.png' alt='' width='25px' title='Register'></a></li></ul>";
        echo "</ul>
        </div>
        </nav>
        ";

        echo "<div class='all-produits'>";
        foreach ($products as $s) {

            echo "
            <div class='produit'>
                <img src='" . $s['photo'] . "' alt='" . $s['nom_produit'] . "'>
                <div class='produit-info'>
                    <h4 class='produit-title'>" . $s['nom_produit'] . "</h4>
                    <p class='produit-price'>" . $s['prix'] . " Dh </p>
                    ";
            // echo '<form method="POST" action="produit_delete.php">';
            // echo '<input type="hidden" name="produit_id" value="' . $s['produit_id'] . '">';
            // echo '<button type="submit" name="delete" class="btn-buy">Buy now</button>';
            // echo '</form>';
            echo "<a href='produit_info.php?id=" . $s['product_id'] . "'>Buy now</a>";
            echo "</div>
                </div>";
                
        }
        echo "</div >";
    }
    ?>

    

    <footer>&copy; Droits d'auteur de Nourim print</footer>

    <script>
        function toggleNavbar() {
            var navbarLinks = document.getElementById("navbar-links");
            navbarLinks.classList.toggle("active");
        }
    </script>

</body>

</html>