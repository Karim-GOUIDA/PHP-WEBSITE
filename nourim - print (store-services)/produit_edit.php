<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit produit</title>
    <link rel="stylesheet" href="produit_edit.css">
</head>

<body>

    <?php
    session_start();

    // Check if user is logged in
    if (!isset($_SESSION["login"]) || !isset($_SESSION["admin"]) || $_SESSION["admin"] !== true) {
        header("Location: index.php");
        exit;
    }

    // Include database connection file
    include("connect.php");

    $produit_id = $_GET["id"] ;


    // echo $_GET["id"] ;

    include("connect.php");
    if (isset($_GET['id'])){
    $produit_id = $_GET['id'];
    $stmt = $con->prepare("SELECT * FROM produits WHERE product_id = ?");
    $stmt->execute([$produit_id]);
    $produit = $stmt->fetch(PDO::FETCH_ASSOC);
    };

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
        try {
            // Retrieve produit ID from the form
            $produit_id = $_POST['produit_id'];

            // Fetch produit details from the database based on the produit ID
            $stmt = $con->prepare("SELECT * FROM produits WHERE product_id = ?");
            $stmt->execute([$produit_id]);
            $produit = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$produit) {
                echo "<p class='error-message'>produit not found.</p>";
            } else {
                // Update produit details in the database
                $nom_produit = $_POST['nom_produit'];
                $description = $_POST['description'];
                $prix = $_POST['prix'];

                $stmt_update = $con->prepare("UPDATE produits SET nom_produit = ?, description = ?, prix = ? WHERE product_id = ?");
                $stmt_update->execute([$nom_produit, $description, $prix, $produit_id]);

                $success = true ;
            }
        } catch (Exception $e) {
            echo "Erreur lors de la mise à jour du produit : " . $e->getMessage();
        }
    }




    ?>

    <!-- BARRE DE NAVIGATION -->
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

    <div class="main">

        <h1>Edit produit</h1>

        <?php

        if (isset($success)){

            echo "<p class='success-message'>Le produit a été mis à jour avec succès.</p>";

        }else{
            echo "" ;
        }
        // Display the edit form
        if (isset($produit)) {
        ?>
            <div class='form-container'>

            <form method="POST" action="" class="edit-produit-form form">

                <input type="hidden" name="produit_id" value="<?php echo $produit['product_id'];  ?>">
                
                <div class='form-group'>
                <label for="nom_produit">Nom du produit:</label>
                <input type="text" id="nom_produit" name="nom_produit" value="<?php echo $produit['nom_produit']; ?>">
                </div>

                <div class='form-group'>
                <label for="description">Description:</label>
                <textarea  id="description" name="description" ><?php echo $produit['description']; ?></textarea>
                </div>

                <div class='form-group'>
                <label for="prix">Prix (Dh):</label>
                <input type="number" id="prix" name="prix" value="<?php echo $produit['prix']; ?>">
                </div>

                <div class='form-group'>
                <button type="submit" name="update">Mettre à jour</button>
                </div>
                
            </form>
        <?php
        } else {
            // echo $_GET["id"] . "</br>" ;
            echo "produit not found.";
        }
        ?>
    </div>

    <footer>&copy; Droits d'auteur de Nourim print</footer>

    <script>
        function toggleNavbar() {
            var navbarLinks = document.getElementById("navbar-links");
            navbarLinks.classList.toggle("active");
        }
    </script>

</body>

</html>
