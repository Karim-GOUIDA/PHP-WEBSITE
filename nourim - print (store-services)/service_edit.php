<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Service</title>
    <link rel="stylesheet" href="service_edit.css">
</head>

<body>

    <?php
    session_start();

    // Check if user is logged in
    if (!isset($_SESSION["login"]) || !isset($_SESSION["admin"]) || $_SESSION["admin"] !== true) {
        header("Location: loginpage.php?m=1");
        exit;
    }

    // Include database connection file
    include("connect.php");

    $service_id = $_GET["id"] ;


    // echo $_GET["id"] ;

    include("connect.php");
    if (isset($_GET['id'])){
    $service_id = $_GET['id'];
    $stmt = $con->prepare("SELECT * FROM services WHERE service_id = ?");
    $stmt->execute([$service_id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);
    };

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
        try {
            // Retrieve service ID from the form
            $service_id = $_POST['service_id'];

            // Fetch service details from the database based on the service ID
            $stmt = $con->prepare("SELECT * FROM services WHERE service_id = ?");
            $stmt->execute([$service_id]);
            $service = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$service) {
                echo "<p class='error-message'>Service not found.</p>";
            } else {
                // Update service details in the database
                $nom_service = $_POST['nom_service'];
                $description = $_POST['description'];
                $prix = $_POST['prix'];

                $stmt_update = $con->prepare("UPDATE services SET nom_service = ?, description = ?, prix = ? WHERE service_id = ?");
                $stmt_update->execute([$nom_service, $description, $prix, $service_id]);

                $success = true ;
            }
        } catch (Exception $e) {
            echo "Erreur lors de la mise à jour du service : " . $e->getMessage();
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

        <h1>Edit Service</h1>

        <?php

        if (isset($success)){

            echo "<p class='success-message'>Le service a été mis à jour avec succès.</p>";

        }else{
            echo "" ;
        }
        // Display the edit form
        if (isset($service)) {
        ?>
            <div class='form-container'>

            <form method="POST" action="" class="edit-service-form form">

                <input type="hidden" name="service_id" value="<?php echo $service['service_id']; ?>">
                
                <div class='form-group'>
                <label for="nom_service">Nom du service:</label>
                <input type="text" id="nom_service" name="nom_service" value="<?php echo $service['nom_service']; ?>">
                </div>

                <div class='form-group'>
                <label for="description">Description:</label>
                <textarea  id="description" name="description" ><?php echo $service['description']; ?></textarea>
                </div>

                <div class='form-group'>
                <label for="prix">Prix (Dh):</label>
                <input type="number" id="prix" name="prix" value="<?php echo $service['prix']; ?>">
                </div>

                <div class='form-group'>
                <button type="submit" name="update">Mettre à jour</button>
                </div>
                
            </form>
        <?php
        } else {
            echo $_GET["id"] . "</br>" ;
            echo "Service not found.";
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
