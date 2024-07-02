<?php
// Démarrer la session si elle n'est pas déjà démarrée
session_start();

include("Connect.php");

if (isset($_SESSION["login"]) === true) {
    header("location:index.php");
}

// Initialiser les variables de drapeau
$username_exists = false;
$email_exists = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $numero_telephone = $_POST['numero_telephone'];
    $adresse = $_POST['adresse'];

    // Valider les saisies (vous pouvez ajouter plus de validations si nécessaire)
    if (empty($username) || empty($email) || empty($password) || empty($numero_telephone) || empty($adresse)) {
        echo "<div class='error-message'>Tous les champs sont obligatoires.</div>";
    } else {
        // Vérifier si le nom d'utilisateur ou l'e-mail existe déjà dans la base de données
        $sql_check_username = "SELECT * FROM utilisateurs WHERE username = :username";
        $sql_check_email = "SELECT * FROM utilisateurs WHERE email = :email";
        $stmt_check_username = $con->prepare($sql_check_username);
        $stmt_check_email = $con->prepare($sql_check_email);
        $stmt_check_username->bindParam(':username', $username);
        $stmt_check_email->bindParam(':email', $email);
        $stmt_check_username->execute();
        $stmt_check_email->execute();

        $user_username = $stmt_check_username->fetch(PDO::FETCH_ASSOC);
        $user_email = $stmt_check_email->fetch(PDO::FETCH_ASSOC);

        if ($user_username) {
            $username_exists = true;
        }
        if ($user_email) {
            $email_exists = true;
        }

        if ($username_exists || $email_exists) {
            echo "<div class='error-message'>Le nom d'utilisateur ou l'e-mail existe déjà. Veuillez choisir un nom d'utilisateur ou un e-mail différent.</div>";
        } else {
            // Hasher le mot de passe pour des raisons de sécurité (vous pouvez utiliser un algorithme de hachage plus sécurisé)
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            try {
                // Préparer la requête SQL pour l'insertion
                $sql_insert_user = "INSERT INTO utilisateurs (username, email, mot_de_passe_hash, numero_telephone, adresse) 
                                    VALUES (:username, :email, :password, :numero_telephone, :adresse)";
                $stmt_insert_user = $con->prepare($sql_insert_user);

                // Binder les paramètres pour l'insertion
                $stmt_insert_user->bindParam(':username', $username);
                $stmt_insert_user->bindParam(':email', $email);
                $stmt_insert_user->bindParam(':password', $hashed_password);
                $stmt_insert_user->bindParam(':numero_telephone', $numero_telephone);
                $stmt_insert_user->bindParam(':adresse', $adresse);

                if ($stmt_insert_user->execute()) {
                    echo "<div class='message'>Inscription réussie !</div>";
                    header("Location: loginpage.php"); // Redirection vers la page de connexion après l'inscription réussie
                    exit(); // Assurer l'arrêt de l'exécution du script après la redirection
                } else {
                    echo "<div class='error-message'>Une erreur s'est produite lors de l'insertion des données utilisateur.</div>";
                }
            } catch (PDOException $e) {
                // Afficher le message d'erreur
                echo "<div class='error-message'>Erreur : " . $e->getMessage() . "</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page d'inscription</title>
    <link rel="stylesheet" href="register.css">
    <style>
        .error-message {
            color: red;
            font-size: 12px;
        }

        .message {
            color: green;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>

<body>

    <nav>
        <a href="index.php"><img src="media/homepage/nourim.png" alt="logo" class="navlogo"></a>
        <ul>
            <!-- <li><a href="Loginpage.php" class="none">Login</a></li> -->
            <!-- <li><a href='loginpage.php'><img src='media/homepage/user.png' alt='' width='25px'></a></li> -->
            <li><a href='index.php'><img src='media/homepage/home.png' alt='' width='25px' title='Home'></a></li>
            <li><a href='loginpage.php'><img src='media/homepage/user.png' alt='' width='25px' title='Login'></a></li>
            <li><a href='registerpage.php'><img src='media/homepage/add-user.png' alt='' width='25px' title='Register'></a></li></ul>
        </ul>
    </nav>

    <main>
        <p id="exist" class="error-message">
            <?php
            if ($username_exists) {
                echo "Le nom d'utilisateur existe déjà. ";
            }
            if ($email_exists) {
                echo "L'e-mail existe déjà.";
            }
            ?>
        </p>
        <div class="form-container">
            <form method="POST" class="form" id="signupForm" onsubmit="return validateForm()">
                <div class="form-group">
                    <label for="username">Nom d'utilisateur</label>
                    <input name="username" id="username" type="text">
                    <span id="usernameError" class="error-message"></span>
                </div>
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input name="email" id="email" type="email">
                    <span id="emailError" class="error-message"></span>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input name="password" id="password" type="password">
                    <span id="passwordError" class="error-message"></span>
                </div>
                <div class="form-group">
                    <label for="numero_telephone">Numéro de téléphone</label>
                    <input name="numero_telephone" id="numero_telephone" type="text">
                    <span id="phoneError" class="error-message"></span>
                </div>
                <div class="form-group">
                    <label for="adresse">Adresse</label>
                    <input name="adresse" id="adresse" type="text">
                    <span id="adresseError" class="error-message"></span>
                </div>
                <button type="submit" class="form-submit-btn">S'inscrire</button>
                <a href="Loginpage.php">Déjà un compte ? Connectez-vous !</a>
            </form>
        </div>
    </main>

    <footer>&copy; Droits d'auteur par Nourim print</footer>

    <script>
        function validateForm() {
            var username = document.getElementById('username').value;
            var email = document.getElementById('email').value;
            var password = document.getElementById('password').value;
            var phone = document.getElementById('numero_telephone').value;
            var address = document.getElementById('adresse').value;

            // Réinitialiser les messages d'erreur
            document.getElementById('usernameError').innerText = '';
            document.getElementById('emailError').innerText = '';
            document.getElementById('passwordError').innerText = '';
            document.getElementById('phoneError').innerText = '';
            document.getElementById('adresseError').innerText = '';

            if (username.trim() == '') {
                document.getElementById('usernameError').innerText = 'Veuillez entrer un nom d\'utilisateur.';
                return false;
            }

            if (email.trim() == '') {
                document.getElementById('emailError').innerText = 'Veuillez entrer votre e-mail.';
                return false;
            }

            if (password.trim() == '') {
                document.getElementById('passwordError').innerText = 'Veuillez entrer votre mot de passe.';
                return false;
            }

            if (phone.trim() == "") {
                document.getElementById('phoneError').innerText = 'Veuillez entrer votre numéro de téléphone.';
                return false;
            } else {
                var regext = /^[0][5-7][0-9]{8}$/;

                if (!regext.test(phone)) {
                    document.getElementById('phoneError').innerText = "champ de telephone invalide";
                    return false;
                }

            }

            if (address.trim() == '') {
                document.getElementById('adresseError').innerText = 'Veuillez entrer votre adresse.';
                return false;
            }

            return true;
        }
    </script>

</body>

</html>