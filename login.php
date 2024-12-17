<?php
session_start();
require 'db_connection.php'; 

// Vérification de la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Vérification des identifiants dans la base de données
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    // Hardcode des identifiants admin
    $adminEmail = 'admin@gmail.com';
    $adminPassword = 'admin123';

    // Vérification des identifiants
    if ($user && password_verify($password, $user['mot_de_passe'])) {
        $_SESSION['user_id'] = $user['id_utilisateur'];
        $_SESSION['nom_utilisateur'] = $user['nom_utilisateur'];
        $_SESSION['user_type'] = 'user'; 
        header('Location: user_page.php'); 
        exit();
    } elseif ($email === $adminEmail && $password === $adminPassword) {
        $_SESSION['user_id'] = 1; 
        $_SESSION['nom_utilisateur'] = 'Admin'; 
        $_SESSION['user_type'] = 'admin'; 
        header('Location: admin_page.php');
        exit();
    } else {
        $error = "Identifiants incorrects.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-blue-500 to-blue-500 flex items-center justify-center h-screen">
    <div class="bg-white p-10 rounded-lg shadow-lg w-96">
        <img src="pic/it-service-1536x864.jpg" alt="Image de connexion" class="w-full h-32 object-cover rounded-lg mb-4">
        <h2 class="text-3xl font-bold text-center mb-6">Connexion</h2>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required class="border border-gray-300 p-3 w-full mb-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            <input type="password" name="password" placeholder="Mot de passe" required class="border border-gray-300 p-3 w-full mb-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            <button type="submit" class="bg-blue-500 text-white p-3 rounded-lg w-full hover:bg-green-700 transition duration-200">Se connecter</button>
        </form>

        <?php if (isset($error)) echo "<p class='text-red-500 mt-4 text-center'>$error</p>"; ?>
        <p class="mt-4 text-center">Pas encore inscrit ? <a href="signup.php" class="text-blue-600 hover:underline">Inscrivez-vous ici</a></p>
    </div>
</body>
</html>