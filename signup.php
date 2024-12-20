<?php
session_start();
require 'db_connection.php'; 

// Vérification de la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom_utilisateur = trim($_POST['nom_utilisateur']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $errors = [];

    // Validation du nom d'utilisateur
    if (strlen($nom_utilisateur) < 3) {
        $errors[] = "Le nom d'utilisateur doit contenir au moins 3 caractères.";
    }

    // Validation de l'email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Veuillez entrer une adresse email valide.";
    }

    // Validation du mot de passe
    if (strlen($password) < 6) {
        $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
    }

    // Si aucune erreur, procéder à l'insertion
    if (empty($errors)) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT); // Hachage du mot de passe

        // Insertion dans la base de données
        $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom_utilisateur, email, mot_de_passe) VALUES (:nom_utilisateur, :email, :mot_de_passe)");
        if ($stmt->execute(['nom_utilisateur' => $nom_utilisateur, 'email' => $email, 'mot_de_passe' => $passwordHash])) {
            header('Location: login.php');
            exit();
        } else {
            $error = "Erreur lors de l'inscription.";
        }
    } else {
        $error = implode("<br>", $errors); 
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-md mx-4 sm:mx-auto p-4 sm:p-8">
        <div class="text-center mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Inscription</h1>
            <p class="text-sm sm:text-base text-gray-600 mt-2">Créez votre compte</p>
        </div>

        <?php if(isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-3 py-2 sm:px-4 sm:py-3 rounded relative mb-4 text-sm sm:text-base" role="alert">
                <span class="block sm:inline"><?php echo $error; ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-4 sm:space-y-6">
            <div>
                <label for="nom_utilisateur" class="block text-sm font-medium text-gray-700 mb-1">Nom d'utilisateur</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-user text-gray-400"></i>
                    </div>
                    <input type="text" name="nom_utilisateur" id="nom_utilisateur" required 
                        class="block w-full pl-10 pr-3 py-2 sm:py-2.5 border border-gray-300 rounded-md text-sm sm:text-base bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                        placeholder="Votre nom d'utilisateur">
                </div>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-envelope text-gray-400"></i>
                    </div>
                    <input type="email" name="email" id="email" required 
                        class="block w-full pl-10 pr-3 py-2 sm:py-2.5 border border-gray-300 rounded-md text-sm sm:text-base bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                        placeholder="Votre email">
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
                    </div>
                    <input type="password" name="password" id="password" required 
                        class="block w-full pl-10 pr-3 py-2 sm:py-2.5 border border-gray-300 rounded-md text-sm sm:text-base bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                        placeholder="Votre mot de passe">
                </div>
            </div>

            <div>
                <button type="submit" class="w-full flex justify-center py-2 sm:py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm sm:text-base font-medium text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-150 ease-in-out">
                    S'inscrire
                </button>
            </div>
        </form>

        <p class="mt-6 text-center text-sm sm:text-base text-gray-600">
            Déjà inscrit? 
            <a href="login.php" class="font-medium text-gray-600 hover:text-gray-500">Se connecter</a>
        </p>
    </div>
</body>
</html>