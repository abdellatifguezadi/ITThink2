<?php
session_start();
require 'db_connection.php'; 

// Vérification si l'utilisateur est connecté et est un administrateur
if (!isset(']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: login.php'); 
    exit();
}

// Récupération des données
$stmt = $pdo->prepare("
    SELECT o.*, p.titre_projet, u.email as email_freelance, u.nom_utilisateur
    FROM offres o
    JOIN projets p ON o.id_projet = p.id_projet
    JOIN utilisateurs u ON o.id_utilisateur = u.id_utilisateur
    ORDER BY o.date_creation DESC
");
$stmt->execute();
$offres = $stmt->fetchAll();

$projets = $pdo->query("
    SELECT p.*, u.nom_utilisateur , c.nom_categorie
    FROM projets p 
    JOIN utilisateurs u ON p.id_utilisateur = u.id_utilisateur
    JOIN categories c ON p.id_categorie = c.id_categorie
")->fetchAll(PDO::FETCH_ASSOC);
$utilisateurs = $pdo->query("SELECT * FROM utilisateurs")->fetchAll(PDO::FETCH_ASSOC);
$freelances = $pdo->query("SELECT * FROM freelances")->fetchAll(PDO::FETCH_ASSOC);
$categorier = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
$sous_categorier = $pdo->query("
    SELECT sc.*, c.nom_categorie
    FROM sous_categories sc
    JOIN categories c ON sc.id_categorie = c.id_categorie
")->fetchAll(PDO::FETCH_ASSOC);
$Temoignages = $pdo->query("SELECT * FROM temoignages")->fetchAll(PDO::FETCH_ASSOC);

$totalUtilisateurs = $pdo->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();
$totalFreelancers = $pdo->query("SELECT COUNT(*) FROM freelances")->fetchColumn();
$totalCategories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$totalSousCategories = $pdo->query("SELECT COUNT(*) FROM sous_categories")->fetchColumn();
$totalProjet = $pdo->query("SELECT COUNT(*) FROM projets")->fetchColumn(); 
$totalOffres = $pdo->query("SELECT COUNT(*) FROM offres")->fetchColumn();

// Gestion des actions de mise à jour et de suppression
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Suppression d'un projet
    if (isset($_POST['delete_projet'])) {
        $id_projet = $_POST['id_projet'];
        $stmt = $pdo->prepare("DELETE FROM projets WHERE id_projet = ?");
        $stmt->execute([$id_projet]);

        header('Location: admin_page.php');
        exit();
    }

    // Suppression d'un témoignage
    if(isset($_POST['delete_Temoignages'])){
        $id_Temoignages = $_POST['id_temoignages'];
        $stmt = $pdo -> prepare("DELETE FROM temoignages WHERE id_temoignages = ?");
        $stmt -> execute([$id_temoignages]);
        header('location : admin_page.php');
        exit();
    }

    // Suppression d'un utilisateur
    if (isset($_POST['delete_utilisateur'])) {
        $id_utilisateur = $_POST['id_utilisateur'];
        $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id_utilisateur = ?");
        $stmt->execute([$id_utilisateur]);

        header('Location: admin_page.php');
        exit();
    }

    // Suppression d'un freelance
    if (isset($_POST['delete_freelancer'])) {
        $id_freelance = $_POST['id_freelance'];
        $stmt = $pdo->prepare("DELETE FROM freelances WHERE id_freelance = ?");
        $stmt->execute([$id_freelance]);

        header('Location: admin_page.php');
        exit();
    }

    // Mise à jour de l'état d'un projet
    if (isset($_POST['etat'])) {
        $id_projet = $_POST['id_projet'];
        $etat = $_POST['etat'];
        $stmt = $pdo->prepare("UPDATE projets SET etat = ? WHERE id_projet = ?");
        $stmt->execute([$etat, $id_projet]);

        header('Location: admin_page.php');
        exit();
    }

    // Ajout d'une catégorie
    if (isset($_POST['add_categorie'])) {
        $nom_categorie = $_POST['nom_categorie'];
        $stmt = $pdo->prepare("INSERT INTO categories (nom_categorie) VALUES (?)");
        $stmt->execute([$nom_categorie]);
        header('Location: admin_page.php');
        exit();
    }

    // Mise à jour d'une catégorie
    if (isset($_POST['update_categorie'])) {
        $id_categorie = $_POST['id_categorie'];
        $nom_categorie = $_POST['nom_categorie'];
        $stmt = $pdo->prepare("UPDATE categories SET nom_categorie = ? WHERE id_categorie = ?");
        $stmt->execute([$nom_categorie, $id_categorie]);

        header('Location: admin_page.php');
        exit();
    }

    // Suppression d'une catégorie
    if (isset($_POST['delete_categorie'])) {
        $id_categorie = $_POST['id_categorie'];
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id_categorie = ?");
        $stmt->execute([$id_categorie]);

        header('Location: admin_page.php');
        exit();
    }

    // Ajout d'une sous-catégorie
    if (isset($_POST['add_sous_categorie'])) {
        $nom_sous_categorie = $_POST['nom_sous_categorie'];
        $id_categorie = $_POST['id_categorie'];
        $stmt = $pdo->prepare("INSERT INTO sous_categories (nom_sous_categorie, id_categorie) VALUES (?, ?)");
        $stmt->execute([$nom_sous_categorie, $id_categorie]);

        header('Location: admin_page.php');
        exit();
    }

    // Mise à jour d'une sous-catégorie
    if (isset($_POST['update_sous_categorie'])) {
        $id_sous_categorie = $_POST['id_sous_categorie'];
        $nom_sous_categorie = $_POST['nom_sous_categorie'];
        $id_categorie = $_POST['id_categorie'];
        $stmt = $pdo->prepare("UPDATE sous_categories SET nom_sous_categorie = ?, id_categorie = ? WHERE id_sous_categorie = ?");
        $stmt->execute([$nom_sous_categorie, $id_categorie, $id_sous_categorie]);

        header('Location: admin_page.php');
        exit();
    }

    // Suppression d'une sous-catégorie
    if (isset($_POST['delete_sous_categorie'])) {
        $id_sous_categorie = $_POST['id_sous_categorie'];
        $stmt = $pdo->prepare("DELETE FROM sous_categories WHERE id_sous_categorie = ?");
        $stmt->execute([$id_sous_categorie]);

        header('Location: admin_page.php');
        exit();
    }
}


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>
<body class="bg-gray-300 flex">
<div class="w-2/12 bg-white h-screen p-5 shadow-lg max-h-screen overflow-y-auto">
    <h2 class="text-xl font-bold mb-4">Menu Admin</h2>
    <ul>
        <li class="h-16 flex items-center mb-2">
            <i class="fas fa-tachometer-alt text-gray-500 mr-2"></i>
            <a href="#" class="text-gray-500 hover:underline" onclick="showSection('dashboard')">Dashboard</a>
        </li>
        <li class="h-16 flex items-center mb-2">
            <i class="fas fa-tags text-gray-500 mr-2"></i>
            <a href="#" class="text-gray-500 hover:underline" onclick="showSection('offres')">Offres</a>
        </li>
        <li class="h-16 flex items-center mb-2">
            <i class="fas fa-project-diagram text-gray-500 mr-2"></i>
            <a href="#" class="text-gray-500 hover:underline" onclick="showSection('projets')">Projets</a>
        </li>
        <li class="h-16 flex items-center mb-2">
            <i class="fas fa-users text-gray-500 mr-2"></i>
            <a href="#" class="text-gray-500 hover:underline" onclick="showSection('utilisateurs')">Utilisateurs</a>
        </li>
        <li class="h-16 flex items-center mb-2">
            <i class="fas fa-user-tie text-gray-500 mr-2"></i>
            <a href="#" class="text-gray-500 hover:underline" onclick="showSection('freelancers')">Freelancers</a>
        </li>
        <li class="h-16 flex items-center mb-2">
            <i class="fa-solid fa-table-list mr-2 text-gray-500"></i>
            <a href="#" class="text-gray-500 hover:underline" onclick="showSection('categorier')">Catégories</a>
        </li>
        <li class="h-16 flex items-center mb-2 text-gray-500">
            <i class="fa-solid fa-list mr-2"></i>
            <a href="#" class="text-gray-500 hover:underline" onclick="showSection('sous_categories')">Sous Catégories</a>
        </li>
        <li class="h-16 flex items-center mb-2 text-gray-500">
            <i class="fa-solid fa-list mr-2"></i>
            <a href="#" class="text-gray-500 hover:underline" onclick="showSection('Temoignages')">Temoignages</a>
        </li>
    </ul>
</div>
<div class="flex-1 p-6">
    <div class="flex justify-end mb-4">
        <form method="POST" action="logout.php">
            <button type="submit" class="bg-red-600 text-white p-2 rounded-lg shadow-md transition-transform transform hover:scale-105 hover:bg-red-700 mt-8 mr-5">
                <i class="fas fa-sign-out-alt mr-2"></i> Se déconnecter
            </button>
        </form>
    </div>
    <h1 class="text-2xl font-bold mb-6">Tableau de Bord Admin</h1>

    <div id="content">
        <div id="dashboard">
            <h2>Bienvenue sur le tableau de bord !</h2>
            <div class="grid grid-cols-3 gap-4 mt-4">
                <div class="bg-white px-4 py-6 rounded-lg shadow-lg flex justify-around items-center transition-transform transform hover:scale-105">
                    <i class="fas fa-users text-gray-500 text-4xl"></i>
                    <div>
                        <h3 class="text-lg font-bold text-gray-500">Total Utilisateurs</h3>
                        <p class="text-3xl font-semibold text-gray-500"><?php echo $totalUtilisateurs; ?></p>
                    </div>
                </div>
                <div class="bg-white px-4 py-6 rounded-lg shadow-lg flex justify-around items-center transition-transform transform hover:scale-105">
                    <i class="fas fa-user-tie text-gray-500 text-4xl"></i>
                    <div>
                        <h3 class="text-lg font-bold text-gray-500">Total Freelancers</h3>
                        <p class="text-3xl font-semibold text-gray-500"><?php echo $totalFreelancers; ?></p>
                    </div>
                </div>
                <div class="bg-white px-4 py-6 rounded-lg shadow-lg flex justify-around items-center transition-transform transform hover:scale-105">
                    <i class="fas fa-th-list text-gray-500 text-4xl"></i>
                    <div>
                        <h3 class="text-lg font-bold text-gray-500">Total Catégories</h3>
                        <p class="text-3xl font-semibold text-gray-500"><?php echo $totalCategories; ?></p>
                    </div>
                </div>
                <div class="bg-white px-4 py-6 rounded-lg shadow-lg flex justify-around items-center transition-transform transform hover:scale-105">
                    <i class="fas fa-list-alt text-gray-500 text-4xl"></i>
                    <div>
                        <h3 class="text-lg font-bold text-gray-500">Total Sous-Catégories</h3>
                        <p class="text-3xl font-semibold text-gray-500"><?php echo $totalSousCategories; ?></p>
                    </div>
                </div>
                <div class="bg-white px-4 py-6 rounded-lg shadow-lg flex justify-around items-center transition-transform transform hover:scale-105">
                    <i class="fas fa-project-diagram text-gray-500 text-4xl"></i>
                    <div>
                        <h3 class="text-lg font-bold text-gray-500">Total Projets</h3>
                        <p class="text-3xl font-semibold text-gray-500"><?php echo $totalProjet; ?></p>
                    </div>
                </div>
                <div class="bg-white px-4 py-6 rounded-lg shadow-lg flex justify-around items-center transition-transform transform hover:scale-105">
                    <i class="fas fa-tags text-gray-500 text-4xl"></i>
                    <div>
                        <h3 class="text-lg font-bold text-gray-500">Total Offres</h3>
                        <p class="text-3xl font-semibold text-gray-500"><?php echo $totalOffres; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div id="offres" class="hidden">
            <h2 class="text-2xl font-bold mb-4">Gestion des Offres</h2>
            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 uppercase text-sm">
                            <th class="py-3 px-4 text-left">Projet</th>
                            <th class="py-3 px-4 text-left">Freelance</th>
                            <th class="py-3 px-4 text-right">Montant</th>
                            <th class="py-3 px-4 text-center">Statut</th>
                            <th class="py-3 px-4 text-left">Description</th>
                            <th class="py-3 px-4 text-center">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($offres as $offre): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4">
                                <span class="font-medium"><?php echo htmlspecialchars($offre['titre_projet']); ?></span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex flex-col">
                                    <span class="font-medium"><?php echo htmlspecialchars($offre['nom_utilisateur']); ?></span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <span class="font-bold text-blue-600"><?php echo number_format($offre['montant'], 2); ?> €</span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-3 py-1 rounded-full text-sm <?php
                                    echo match($offre['statut']) {
                                        'en_attente' => 'bg-yellow-100 text-yellow-800',
                                        'acceptee' => 'bg-green-100 text-green-800',
                                        'refusee' => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                ?>"><?php echo ucfirst($offre['statut']); ?></span>
                            </td>
                            <td class="py-3 px-4">
                                <p class="text-sm text-gray-600 truncate max-w-xs">
                                    <?php echo $offre['description'] ? htmlspecialchars($offre['description']) : '<span class="text-gray-400">Aucune description</span>'; ?>
                                </p>
                            </td>
                            <td class="py-3 px-4 text-center text-sm text-gray-500">
                                <?php echo date('d/m/Y', strtotime($offre['date_creation'])); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="projets" class="hidden">
            <h2 class="text-2xl font-bold mb-4">Gestion des Projets</h2>
            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 uppercase text-sm">
                            <th class="py-3 px-4 text-left">ID</th>
                            <th class="py-3 px-4 text-left">Titre</th>
                            <th class="py-3 px-4 text-left">Description</th>
                            <th class="py-3 px-4 text-left">Catégorie</th>
                            <th class="py-3 px-4 text-center">État</th>
                            <th class="py-3 px-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($projets as $projet): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4"><?php echo $projet['id_projet']; ?></td>
                            <td class="py-3 px-4">
                                <span class="font-medium"><?php echo htmlspecialchars($projet['titre_projet']); ?></span>
                            </td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($projet['description']); ?></td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($projet['nom_categorie']); ?></td>
                            <td class="py-3 px-4 text-center">
                                <form method="POST" class="inline">
                                    <input type="hidden" name="id_projet" value="<?php echo $projet['id_projet']; ?>">
                                    <select name="etat" onchange="this.form.submit()" class="px-3 py-1 rounded text-sm
                                        <?php echo match($projet['etat']) {
                                            'en_cours' => 'bg-yellow-100 text-yellow-800',
                                            'termine' => 'bg-green-100 text-green-800',
                                            'a faire' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        }; ?>">
                                        <option value="en_cours" <?php echo $projet['etat'] == 'en_cours' ? 'selected' : ''; ?>>En cours</option>
                                        <option value="termine" <?php echo $projet['etat'] == 'termine' ? 'selected' : ''; ?>>Terminé</option>
                                        <option value="a faire" <?php echo $projet['etat'] == 'a faire' ? 'selected' : ''; ?>>à faire</option>
                                    </select>
                                </form>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <form method="POST" action="confirm_delete.php" class="inline">
                                    <input type="hidden" name="id_projet" value="<?php echo $projet['id_projet']; ?>">
                                    <input type="hidden" name="type" value="projet">
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="utilisateurs" class="hidden">
            <h2 class="text-2xl font-bold mb-4">Gestion des Utilisateurs</h2>
            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 uppercase text-sm">
                            <th class="py-3 px-4 text-left">ID</th>
                            <th class="py-3 px-4 text-left">Nom d'utilisateur</th>
                            <th class="py-3 px-4 text-left">Email</th>
                            <th class="py-3 px-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($utilisateurs as $utilisateur): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4"><?php echo $utilisateur['id_utilisateur']; ?></td>
                            <td class="py-3 px-4">
                                <span class="font-medium"><?php echo htmlspecialchars($utilisateur['nom_utilisateur']); ?></span>
                            </td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($utilisateur['email']); ?></td>
                            <td class="py-3 px-4 text-center">
                                <form method="POST" action="confirm_delete.php" class="inline">
                                    <input type="hidden" name="id_utilisateur" value="<?php echo $utilisateur['id_utilisateur']; ?>">
                                    <input type="hidden" name="type" value="utilisateur">
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="freelancers" class="hidden">
            <h2 class="text-2xl font-bold mb-4">Gestion des Freelances</h2>
            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 uppercase text-sm">
                            <th class="py-3 px-4 text-left">ID</th>
                            <th class="py-3 px-4 text-left">Nom</th>
                            <th class="py-3 px-4 text-left">Compétences</th>
                            <th class="py-3 px-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($freelances as $freelance): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4"><?php echo $freelance['id_freelance']; ?></td>
                            <td class="py-3 px-4">
                                <span class="font-medium"><?php echo htmlspecialchars($freelance['nom_freelance']); ?></span>
                            </td>
                            <td class="py-3 px-4">
                                <p class="text-sm text-gray-600"><?php echo htmlspecialchars($freelance['competences']); ?></p>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <form method="POST" action="confirm_delete.php" class="inline">
                                    <input type="hidden" name="id_freelance" value="<?php echo $freelance['id_freelance']; ?>">
                                    <input type="hidden" name="type" value="freelance">
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="Temoignages" class="hidden">
            <h2 class="text-2xl font-bold mb-4">Gestion des Témoignages</h2>
            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 uppercase text-sm">
                            <th class="py-3 px-4 text-left">ID</th>
                            <th class="py-3 px-4 text-left">Utilisateur</th>
                            <th class="py-3 px-4 text-left">Commentaire</th>
                            <th class="py-3 px-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($Temoignages as $temoignage): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4"><?php echo $temoignage['id_temoignage']; ?></td>
                            <td class="py-3 px-4">
                                <span class="font-medium"><?php echo htmlspecialchars($temoignage['id_utilisateur']); ?></span>
                            </td>
                            <td class="py-3 px-4">
                                <p class="text-sm text-gray-600 truncate max-w-xs"><?php echo htmlspecialchars($temoignage['commentaire']); ?></p>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <form method="POST" action="confirm_delete.php" class="inline">
                                    <input type="hidden" name="id_temoignages" value="<?php echo $temoignage['id_temoignage']; ?>">
                                    <input type="hidden" name="type" value="temoignages">
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="categorier" class="hidden">
            <h2 class="text-2xl font-bold mb-4">Liste des Catégories</h2>
            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 uppercase text-sm">
                            <th class="py-3 px-4 text-left">ID</th>
                            <th class="py-3 px-4 text-left">Nom</th>
                            <th class="py-3 px-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($categorier as $categorie): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4"><?php echo $categorie['id_categorie']; ?></td>
                            <td class="py-3 px-4"><?php echo $categorie['nom_categorie']; ?></td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex justify-center items-center space-x-4">
                                    <button onclick="showUpdateCategoryForm(<?php echo $categorie['id_categorie']; ?>, '<?php echo addslashes($categorie['nom_categorie']); ?>')" class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <form method="POST" action="confirm_delete.php" class="inline">
                                        <input type="hidden" name="id_categorie" value="<?php echo $categorie['id_categorie']; ?>">
                                        <input type="hidden" name="type" value="categorie">
                                        <button type="submit" class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <button onclick="showAddCategoryForm()" class="bg-green-500 text-white p-2 rounded m-4">Ajouter une Catégorie</button>

                <div id="addCategoryForm" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white p-6 rounded-lg shadow-lg w-96 relative">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Ajouter une Catégorie</h3>
                        <form method="POST" action="">
                            <div class="mb-4">
                                <label for="nom_categorie" class="block text-sm font-medium text-gray-700">Nom de la Catégorie</label>
                                <input type="text" name="nom_categorie" placeholder="Nom de la catégorie" required 
                                    class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-500">
                            </div>
                            <div class="flex justify-between">
                                <button type="submit" name="add_categorie" class="bg-blue-500 text-white p-2 rounded hover:bg-blue-600 transition duration-200">Ajouter</button>
                                <button type="button" onclick="hideAddCategoryForm()" class="bg-gray-500 text-white p-2 rounded hover:bg-gray-600 transition duration-200">Annuler</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="updateCategoryForm" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white p-6 rounded-lg shadow-lg w-96 relative">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Mettre à jour la Catégorie</h3>
                        <form method="POST" action="">
                            <input type="hidden" name="id_categorie" id="update_id_categorie">
                            <div class="mb-4">
                                <label for="update_nom_categorie" class="block text-sm font-medium text-gray-700">Nom de la Catégorie</label>
                                <input type="text" name="nom_categorie" id="update_nom_categorie" placeholder="Nom de la catégorie" required 
                                    class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-500">
                            </div>
                            <div class="flex justify-between">
                                <button type="submit" name="update_categorie" class="bg-blue-500 text-white p-2 rounded hover:bg-blue-600 transition duration-200">Sauvegarder</button>
                                <button type="button" onclick="hideUpdateCategoryForm()" class="bg-gray-500 text-white p-2 rounded hover:bg-gray-600 transition duration-200">Annuler</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="sous_categories" class="hidden">
            <h2 class="text-2xl font-bold mb-4">Liste des Sous-Catégories</h2>
            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 uppercase text-sm">
                            <th class="py-3 px-4 text-left">ID</th>
                            <th class="py-3 px-4 text-left">Nom</th>
                            <th class="py-3 px-4 text-left">Catégorie</th> 
                            <th class="py-3 px-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($sous_categorier as $sous_categorie): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4"><?php echo $sous_categorie['id_sous_categorie']; ?></td>
                            <td class="py-3 px-4"><?php echo $sous_categorie['nom_sous_categorie']; ?></td>
                            <td class="py-3 px-4"><?php echo $sous_categorie['nom_categorie']; ?></td> 
                            <td class="py-3 px-4 text-center">
                                <div class="flex justify-center items-center space-x-4">
                                    <button onclick="showUpdateSousCategoryForm(<?php echo $sous_categorie['id_sous_categorie']; ?>, '<?php echo addslashes($sous_categorie['nom_sous_categorie']); ?>', <?php echo $sous_categorie['id_categorie']; ?>)" class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <form method="POST" action="confirm_delete.php" class="inline">
                                        <input type="hidden" name="id_sous_categorie" value="<?php echo $sous_categorie['id_sous_categorie']; ?>">
                                        <input type="hidden" name="type" value="sous_categorie">
                                        <button type="submit" class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <button onclick="showAddSousCategoryForm()" class="bg-green-500 text-white p-2 rounded m-4">Ajouter une Sous-Catégorie</button>

                <div id="addSousCategoryForm" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white p-6 rounded-lg shadow-lg w-96 relative">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Ajouter une Sous-Catégorie</h3>
                        <form method="POST" action="">
                            <div class="mb-4">
                                <label for="nom_sous_categorie" class="block text-sm font-medium text-gray-700">Nom de la Sous-Catégorie</label>
                                <input type="text" name="nom_sous_categorie" placeholder="Nom de la sous-catégorie" required 
                                    class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-500">
                            </div>
                            <div class="mb-4">
                                <label for="id_categorie" class="block text-sm font-medium text-gray-700">Catégorie</label>
                                <select name="id_categorie" required 
                                    class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-500">
                                    <option value="">Sélectionner une catégorie</option>
                                    <?php foreach ($categorier as $categorie): ?>
                                        <option value="<?php echo $categorie['id_categorie']; ?>"><?php echo $categorie['nom_categorie']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="flex justify-between">
                                <button type="submit" name="add_sous_categorie" class="bg-blue-500 text-white p-2 rounded hover:bg-blue-600 transition duration-200">Ajouter</button>
                                <button type="button" onclick="hideAddSousCategoryForm()" class="bg-gray-500 text-white p-2 rounded hover:bg-gray-600 transition duration-200">Annuler</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="updateSousCategoryForm" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white p-6 rounded-lg shadow-lg w-96 relative">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Mettre à jour la Sous-Catégorie</h3>
                        <form method="POST" action="">
                            <input type="hidden" name="id_sous_categorie" id="update_id_sous_categorie">
                            <div class="mb-4">
                                <label for="update_nom_sous_categorie" class="block text-sm font-medium text-gray-700">Nom de la Sous-Catégorie</label>
                                <input type="text" name="nom_sous_categorie" id="update_nom_sous_categorie" placeholder="Nom de la sous-catégorie" required 
                                    class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-500">
                            </div>
                            <div class="mb-4">
                                <label for="update_id_categorie" class="block text-sm font-medium text-gray-700">Catégorie</label>
                                <select name="id_categorie" id="update_id_categorie" required 
                                    class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-500">
                                    <option value="">Sélectionner une catégorie</option>
                                    <?php foreach ($categorier as $categorie): ?>
                                        <option value="<?php echo $categorie['id_categorie']; ?>"><?php echo $categorie['nom_categorie']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="flex justify-between">
                                <button type="submit" name="update_sous_categorie" class="bg-blue-500 text-white p-2 rounded hover:bg-blue-600 transition duration-200">Sauvegarder</button>
                                <button type="button" onclick="hideUpdateSousCategoryForm()" class="bg-gray-500 text-white p-2 rounded hover:bg-gray-600 transition duration-200">Annuler</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="admin.js"></script>

</body>
</html>