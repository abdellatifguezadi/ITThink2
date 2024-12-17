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

    // Validation du mdp
    if (strlen($password) < 6) {
        $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
    }

    // Insertion dans la base de données
    if (empty($errors)) {

        $passwordHash = password_hash($password, PASSWORD_DEFAULT); 
        $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom_utilisateur, email, mot_de_passe) VALUES (:nom_utilisateur, :email, :mot_de_passe)");
        if ($stmt->execute(['nom_utilisateur' => $nom_utilisateur, 'email' => $email, 'mot_de_passe' => $passwordHash])) {
            $success = "Inscription réussie. Vous pouvez vous connecter.";
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
    <title>Inscription</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector("form");
            form.addEventListener("submit", function(event) {
                const nomUtilisateur = form.nom_utilisateur.value.trim();
                const email = form.email.value.trim();
                const password = form.password.value.trim();
                let valid = true;

                // Validation du nom d'utilisateur
                if (nomUtilisateur.length < 3) {
                    alert("Le nom d'utilisateur doit contenir au moins 3 caractères.");
                    valid = false;
                }

                // Validation de l'email
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(email)) {
                    alert("Veuillez entrer une adresse email valide.");
                    valid = false;
                }

                // Validation du mdp
                if (password.length < 6) {
                    alert("Le mot de passe doit contenir au moins 6 caractères.");
                    valid = false;
                }

                if (!valid) {
                    event.preventDefault(); 
                }
            });
        });
    </script>
</head>
<body class="bg-blue-500 flex items-center justify-center h-screen">
    <div class="bg-white p-10 rounded-lg shadow-lg w-96">
        <img src="pic/siag_917x600.jpg" alt="Image d'inscription" class="w-full h-32 object-cover rounded-lg mb-4"> 
        <h2 class="text-3xl font-bold text-center mb-6">Inscription</h2>
        <form method="POST">
            <input type="text" name="nom_utilisateur" placeholder="Nom d'utilisateur" required class="border border-gray-300 p-3 w-full mb-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            <input type="email" name="email" placeholder="Email" required class="border border-gray-300 p-3 w-full mb-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            <input type="password" name="password" placeholder="Mot de passe" required class="border border-gray-300 p-3 w-full mb-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            <button type=" submit" class="bg-blue-500 text-white p-3 rounded-lg w-full hover:bg-green-700 transition duration-200">S'inscrire</button>
        </form>

        <?php if (isset($error)) echo "<p class='text-red-500 mt-4 text-center'>$error</p>"; ?>
        <?php if (isset($success)) echo "<p class='text-green-500 mt-4 text-center'>$success</p>"; ?>
        <p class="mt-4 text-center">Déjà inscrit ? <a href="login.php" class="text-blue-600 hover:underline">Connectez-vous ici</a></p>
    </div>
</body>
</html>