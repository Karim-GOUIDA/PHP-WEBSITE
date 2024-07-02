<?php
session_start();

include("Connect.php");

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    header("location: loginpage.php");
    exit;
}
if (!isset($_SESSION["admin"]) || $_SESSION["admin"] == true) {
    header("location: index.php");
    exit;
}

// Récupérer les données de l'utilisateur depuis la base de données
$user_id = $_SESSION["id"]; // Supposons que vous avez un user_id stocké dans la session
$sql_fetch_user = "SELECT * FROM utilisateurs WHERE user_id = :user_id";
$stmt_fetch_user = $con->prepare($sql_fetch_user);
$stmt_fetch_user->bindParam(':user_id', $user_id);
$stmt_fetch_user->execute();
$user_data = $stmt_fetch_user->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $username = $_POST['username'];
    $email = $_POST['email'];
    $numero_telephone = $_POST['numero_telephone'];
    $adresse = $_POST['adresse'];
    $new_password = $_POST['new_password'];

    // Valider les saisies (vous pouvez ajouter plus de validations au besoin)
    if (empty($username) || empty($email) || empty($numero_telephone) || empty($adresse)) {
        $error_message = "Tous les champs sont obligatoires.";
    } else {
        // Mettre à jour les données de l'utilisateur dans la base de données
        $sql_update_user = "UPDATE utilisateurs SET username = :username, email = :email, numero_telephone = :numero_telephone, adresse = :adresse";

        // Vérifier si un nouveau mot de passe est fourni
        if (!empty($new_password)) {
            $sql_update_user .= ", mot_de_passe_hash = :mot_de_passe_hash";
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        }

        $sql_update_user .= " WHERE user_id = :user_id";
        $stmt_update_user = $con->prepare($sql_update_user);
        $stmt_update_user->bindParam(':username', $username);
        $stmt_update_user->bindParam(':email', $email);
        $stmt_update_user->bindParam(':numero_telephone', $numero_telephone);
        $stmt_update_user->bindParam(':adresse', $adresse);
        $stmt_update_user->bindParam(':user_id', $user_id);

        if (!empty($new_password)) {
            $stmt_update_user->bindParam(':mot_de_passe_hash', $hashed_password);
        }

        if ($stmt_update_user->execute()) {
            $success_message = "Vos données ont été mises à jour avec succès.";
            // Rafraîchir les données de l'utilisateur après la mise à jour
            $user_data['username'] = $username;
            $user_data['email'] = $email;
            $user_data['numero_telephone'] = $numero_telephone;
            $user_data['adresse'] = $adresse;
        } else {
            $error_message = "Une erreur s'est produite lors de la mise à jour des données.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page de profil</title>
    <!-- Inclure votre fichier CSS -->
    <link rel="stylesheet" href="profil.css">
    <style>
        .error-message {
            color: red;
        }

        .success-message {
            color: green;
        }

        .hi {
            margin-top: 100px;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <nav>
        <a href="index.php"><img src="media/homepage/nourim.png" alt="logo" class="navlogo"></a>
        <ul>  
            <li><a href='index.php'><img src='media/homepage/home.png' alt='' width='25px' title='Home'></a></li>
            <li><a href='commande.php'><img src='media/homepage/commande.png' alt='' width='25px' title='Commande'></a></li>
            <li><a href='profil.php'><img src='media/homepage/user.png' alt='' width='25px' title='Profil'></a></li>
            <li><a href='panier.php'><img src='media/homepage/shopping-cart.png' alt='' width='25px' title='Shopping Cart'></a></li>
            <li><a href='logout.php'><img src='media/homepage/logout.png' alt='' width='25px' title='Logout'></a></li>
        </ul>
    </nav>

    <h1 class='hi'>Bienvenue, <?php echo $user_data['username']; ?>!</h1>
    <div>
        <?php
        if (isset($error_message)) {
            echo "<div class='error-message'>$error_message</div>";
        } elseif (isset($success_message)) {
            echo "<div class='success-message'>$success_message</div>";
        }
        ?>

        <div class="form-container">
            <form method="POST" class="form" id="loginForm" onsubmit="return validateForm()">
                <div class="form-group">
                    <label for="username">Nom d'utilisateur</label>
                    <input name="username" id="username" type="text" value="<?php echo $user_data['username']; ?>">
                    <div id="usernameError" class="error-message"></div>
                </div>
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input name="email" id="email" type="email" value="<?php echo $user_data['email']; ?>">
                    <div id="emailError" class="error-message"></div>
                </div>
                <div class="form-group">
                    <label for="numero_telephone">Numéro de téléphone</label>
                    <input name="numero_telephone" id="numero_telephone" type="text" value="<?php echo $user_data['numero_telephone']; ?>">
                    <div id="phoneError" class="error-message"></div>
                </div>
                <div class="form-group">
                    <label for="adresse">Adresse</label>
                    <input name="adresse" id="adresse" type="text" value="<?php echo $user_data['adresse']; ?>">
                    <div id="adresseError" class="error-message"></div>
                </div>
                <div class="form-group">
                    <label for="new_password">Nouveau mot de passe</label>
                    <input name="new_password" id="new_password" type="password">
                    <div id="passwordError" class="error-message"></div>
                </div>
                <button type="submit" class="form-submit-btn">Mettre à jour</button>
            </form>
        </div>
    </div>

    <footer>&copy; Droits d'auteur par Nourim print</footer>

    <script>
        function validateForm() {
            var username = document.getElementById('username').value;
            var email = document.getElementById('email').value;
            var password = document.getElementById('new_password').value; // Updated ID here
            var newPassword = document.getElementById('new_password').value;
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

            if (password.trim() == '') { // Use 'new_password' instead of 'password'
                document.getElementById('passwordError').innerText = 'Veuillez entrer votre mot de passe actuel.';
                return false;
            }

            if (newPassword.trim() != '') {
                // Check if new password meets criteria
                var passwordRegex = /^[A-Za-z]{8,}$/;
                if (!passwordRegex.test(newPassword)) {
                    document.getElementById('passwordError').innerText = 'Le nouveau mot de passe doit contenir au moins 8 caractères .';
                    return false;
                }
            }

            if (phone.trim() == "") {
                document.getElementById('phoneError').innerText = 'Veuillez entrer votre numéro de téléphone.';
                return false;
            } else {
                var phoneRegex = /^[0][5-7][0-9]{8}$/;

                if (!phoneRegex.test(phone)) {
                    document.getElementById('phoneError').innerText = "Le numéro de téléphone est invalide. Veuillez entrer un numéro valide.";
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