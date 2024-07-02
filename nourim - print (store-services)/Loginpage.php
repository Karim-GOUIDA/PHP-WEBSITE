<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page de connexion</title>
    <link rel="stylesheet" href="Layout.css">
    <link rel="stylesheet" href="Loginpage.css">
    <style>
        .error-message {
            color: red;
            font-size: 12px;
        }

        .err {
            margin-left: 120px;
            color: rgb(220, 0, 0);
        }

        .message {
            color: red;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>

<body>

    <?php

    if (isset($_SESSION["login"]) === true) {

        header("location:index.php");
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_POST["email"]) && !empty($_POST["password"])) {
            // Récupérer les données du formulaire
            $email = $_POST['email'];
            $password = $_POST['password'];
            $user_exists = true;
            try {
                include "Connect.php"; // Inclure votre script de connexion à la base de données

                // Préparer la requête SQL pour récupérer les données de l'utilisateur
                $sql = "SELECT * FROM utilisateurs WHERE email = :email";
                $stmt = $con->prepare($sql);
                $stmt->bindParam(':email', $email);
                $stmt->execute();

                // Récupérer les données de l'utilisateur
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    // Vérifier le mot de passe avec password_verify
                    if (password_verify($password, $user['mot_de_passe_hash'])) {
                        // Le mot de passe est correct, définir les variables de session et rediriger
                        $_SESSION["nom"] = $user["username"];
                        $_SESSION["id"] = $user["user_id"];
                        $_SESSION["login"] = true;
                        if ($user["est_admin"] == 1) {
                            $_SESSION["admin"] = true;
                        } else {
                            $_SESSION["admin"] = false;
                        }
                        header("Location: index.php");
                        exit();
                    } else {
                        echo "<div class='error-message'>Adresse email ou mot de passe incorrect.</div>";
                        $user_exists = false;
                    }
                } else {
                    $user_exists = false;
                }
            } catch (PDOException $e) {
                echo "<div class='error-message'>Erreur: " . $e->getMessage() . "</div>";
            }
        } else {
            echo "<div class='error-message'>Entrée invalide! Veuillez fournir une adresse email et un mot de passe.</div>";
        }
    }
    ?>


    <nav>

        <a href="index.php"><img src="media/homepage/nourim.png" alt="logo" class="navlogo"></a>

        <div>
            <div class='navbar-toggle' onclick='toggleNavbar()'>
                <div></div>
                <div></div>
                <div></div>
            </div>

            <ul id='navbar-links'>
                <li><a href='index.php'><img src='media/homepage/home.png' alt='' width='25px' title='Home'></a></li>
                <li><a href='loginpage.php'><img src='media/homepage/user.png' alt='' width='25px' title='Login'></a></li>
                <li><a href='registerpage.php'><img src='media/homepage/add-user.png' alt='' width='25px' title='Register'></a></li>
            </ul>

        </div>
    </nav>

    <main>
        <div class="form-container">
            <form method="POST" class="form" id="loginForm" onsubmit="return validateForm()">
                <div class="form-group">
                    <label for="email">Adresse Email</label>
                    <input name="email" id="email" type="email">
                    <span id="emailError" class="error-message"></span>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input name="password" id="password" type="password">
                    <span id="passwordError" class="error-message"></span>
                </div>
                <button type="submit" class="form-submit-btn">Envoyer</button>
                <p id="notexist" class="message">

                    <?php
                    if (isset($user_exists)) {
                        echo "L'utilisateur n'existe pas. ";
                    } else {
                        echo "";
                    }
                    ?>

                </p>
                <a href="Registerpage.php">Créer un compte!</a>
            </form>
        </div>
    </main>

    <footer>&copy; Droits d'auteur par Nourim Print</footer>

    <script>
        
        function toggleNavbar() {
            var navbarLinks = document.getElementById("navbar-links");
            navbarLinks.classList.toggle("active");
        }


        function validateForm(event) {
            var email = document.getElementById('email').value;
            var password = document.getElementById('password').value;

            if (email.trim() == '') {
                document.getElementById('emailError').innerText = 'Veuillez entrer votre adresse email.';
                return false;
            } else {
                document.getElementById('emailError').innerText = '';
            }

            if (password.trim() == '') {
                document.getElementById('passwordError').innerText = 'Veuillez entrer votre mot de passe.';
                return false;
            } else {
                document.getElementById('passwordError').innerText = '';
            }

            return true;
        }
    </script>



</body>

</html>