<?php
session_start();
require 'db_connection.php'; 

// Vérification si l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: login.php'); 
    exit();
}

// Récupération des données
$offres = $pdo->query("SELECT * FROM offres")->fetchAll(PDO::FETCH_ASSOC);
$projets = $pdo->query("
    SELECT p.*, u.nom_utilisateur , c.nom_categorie
    FROM projets p 
    JOIN utilisateurs u ON p.id_utilisateur = u.id_utilisateur
    JOIN categories c ON p.id_categorie = c.id_categorie
")->fetchAll(PDO::FETCH_ASSOC);
$utilisateurs = $pdo->query("SELECT * FROM utilisateurs")->fetchAll(PDO::FETCH_ASSOC);
$freelances = $pdo->query("SELECT * FROM freelances")->fetchAll(PDO::FETCH_ASSOC);
$categorier = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
$sous_categorier = $pdo->query("SELECT * FROM sous_categories")->fetchAll(PDO::FETCH_ASSOC);

$totalUtilisateurs = $pdo->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();
$totalFreelancers = $pdo->query("SELECT COUNT(*) FROM freelances")->fetchColumn();
$totalCategories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$totalSousCategories = $pdo->query("SELECT COUNT(*) FROM sous_categories")->fetchColumn();
$totalProjet = $pdo->query("SELECT COUNT(*) FROM projets")->fetchColumn(); 
$totalOffres = $pdo->query("SELECT COUNT(*) FROM offres")->fetchColumn();

// Gestion des actions de mise à jour et de suppression
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mise à jour du projet
    if (isset($_POST['update_projet'])) {
        $id_projet = $_POST['id_projet'];
        $titre_projet = $_POST['titre_projet'];
        $description = $_POST['description'];

        $stmt = $pdo->prepare("UPDATE projets SET titre_projet = ?, description = ? WHERE id_projet = ?");
        $stmt->execute([$titre_projet, $description, $id_projet]);

        header('Location: admin_page.php');
        exit();
    }

    // Suppression du projet
    if (isset($_POST['delete_projet'])) {
        $id_projet = $_POST['id_projet'];

        $stmt = $pdo->prepare("DELETE FROM projets WHERE id_projet = ?");
        $stmt->execute([$id_projet]);

        header('Location: admin_page.php');
        exit();
    }

    // Ajout d'un nouveau projet
    if (isset($_POST['add_projet'])) {
        $titre_projet = $_POST['titre_projet'];
        $description = $_POST['description'];
        $id_categorie = $_POST['id_categorie'];
        $id_utilisateur = $_POST['id_utilisateur'];

        $stmt = $pdo->prepare("INSERT INTO projets (titre_projet, description, id_categorie, id_utilisateur) VALUES (?, ?, ?, ?)");
        $stmt->execute([$titre_projet, $description, $id_categorie, $id_utilisateur]);

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
<body class="bg-white flex">
<div class="w-2/12 bg-blue-600 h-screen p-5 shadow-lg max-h-screen overflow-y-auto">
    <h2 class="text-xl font-bold mb-4">Menu Admin</h2>
    <ul>
        <li class="h-16 flex items-center mb-2">
            <i class="fas fa-tachometer-alt text-white mr-2"></i>
            <a href="#" class="text-white hover:underline" onclick="showSection('dashboard')">Dashboard</a>
        </li>
        <li class="h-16 flex items-center mb-2">
            <i class="fas fa-tags text-white mr-2"></i>
            <a href="#" class="text-white hover:underline" onclick="showSection('offres')">Offres</a>
        </li>
        <li class="h-16 flex items-center mb-2">
            <i class="fas fa-project-diagram text-white mr-2"></i>
            <a href="#" class="text-white hover:underline" onclick="showSection('projets')">Projets</a>
        </li>
        <li class="h-16 flex items-center mb-2">
            <i class="fas fa-users text-white mr-2"></i>
            <a href="#" class="text-white hover:underline" onclick="showSection('utilisateurs')">Utilisateurs</a>
        </li>
        <li class="h-16 flex items-center mb-2">
            <i class="fas fa-user-tie text-white mr-2"></i>
            <a href="#" class="text-white hover:underline" onclick="showSection('freelancers')">Freelancers</a>
        </li>
        <li class="h-16 flex items-center mb-2">
            <i class="fa-solid fa-table-list mr-2"></i>
            <a href="#" class="text-white hover:underline" onclick="showSection('categorier')">Catégories</a>
        </li>
        <li class="h-16 flex items-center mb-2">
            <i class="fa-solid fa-list mr-2"></i>
            <a href="#" class="text-white hover:underline" onclick="showSection('sous_categories')">Sous Catégories</a>
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
                <div class="bg-blue-600 px-4 py-6 rounded-lg shadow-lg flex justify-around items-center transition-transform transform hover:scale-105">
                    <i class="fas fa-users text-white text-4xl"></i>
                    <div>
                        <h3 class="text-lg font-bold text-white">Total Utilisateurs</h3>
                        <p class="text-3xl font-semibold text-white"><?php echo $totalUtilisateurs; ?></p>
                    </div>
                </div>
                <div class="bg-blue-600 px-4 py-6 rounded-lg shadow-lg flex justify-around items-center transition-transform transform hover:scale-105">
                    <i class="fas fa-user-tie text-white text-4xl"></i>
                    <div>
                        <h3 class="text-lg font-bold text-white">Total Freelancers</h3>
                        <p class="text-3xl font-semibold text-white"><?php echo $totalFreelancers; ?></p>
                    </div>
                </div>
                <div class="bg-blue-600 px-4 py-6 rounded-lg shadow-lg flex justify-around items-center transition-transform transform hover:scale-105">
                    <i class="fas fa-th-list text-white text-4xl"></i>
                    <div>
                        <h3 class="text-lg font-bold text-white">Total Catégories</h3>
                        <p class="text-3xl font-semibold text-white"><?php echo $totalCategories; ?></p>
                    </div>
                </div>
                <div class="bg-blue-600 px-4 py-6 rounded-lg shadow-lg flex justify-around items-center transition-transform transform hover:scale-105">
                    <i class="fas fa-list-alt text-white text-4xl"></i>
                    <div>
                        <h3 class="text-lg font-bold text-white">Total Sous-Catégories</h3>
                        <p class="text-3xl font-semibold text-white"><?php echo $totalSousCategories; ?></p>
                    </div>
                </div>
                <div class="bg-blue-600 px-4 py-6 rounded-lg shadow-lg flex justify-around items-center transition-transform transform hover:scale-105">
                    <i class="fas fa-project-diagram text-white text-4xl"></i>
                    <div>
                        <h3 class="text-lg font-bold text-white">Total Projets</h3>
                        <p class="text-3xl font-semibold text-white"><?php echo $totalProjet; ?></p>
                    </div>
                </div>
                <div class="bg-blue-600 px-4 py-6 rounded-lg shadow-lg flex justify-around items-center transition-transform transform hover:scale-105">
                    <i class="fas fa-tags text-white text-4xl"></i>
                    <div>
                        <h3 class="text-lg font-bold text-white">Total Offres</h3>
                        <p class="text-3xl font-semibold text-white"><?php echo $totalOffres; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div id="offres" class="hidden">
            <h2 class="text-xl font-bold mb-4">Liste des Offres</h2>
            <table class="min-w-full border-collapse border border-gray-200 bg-white">
                <thead>
                    <tr>
                        <th class="border border-gray-200 p-2">ID Offre</th>
                        <th class="border border-gray-200 p-2">Montant</th>
                        <th class="border border-gray-200 p-2">Freelance ID</th>
                        <th class="border border-gray-200 p-2">Projet ID</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($offres as $offre): ?>
                    <tr>
                        <td class="border border-gray-200 p-2"><?php echo $offre['id_offre']; ?></td>
                        <td class="border border-gray-200 p-2"><?php echo $offre['montant']; ?></td>
                        <td class="border border-gray-200 p-2"><?php echo $offre['id_utilisateur']; ?></td>
                        <td class="border border-gray-200 p-2"><?php echo $offre['id_projet']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div id="projets" class="hidden">
            <h2 class="text-xl font-bold mb-4">Liste des Projets</h2>
            <table class="min-w-full border-collapse border border-gray-200 bg-white">
                <thead>
                    <tr>
                        <th class="border border-gray-200 p-2">ID Projet</th>
                        <th class="border border-gray-200 p-2">Titre</th>
                        <th class="border border-gray-200 p-2">Description</th>
                        <th class="border border-gray-200 p-2">Catégorie ID</th>
                        <th class="border border-gray-200 p-2">Utilisateur ID</th>
                        <th class="border border-gray-200 p-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projets as $projet): ?>
                    <tr>
                        <td class="border border-gray-200 p-2"><?php echo $projet['id_projet']; ?></td>
                        <td class="border border-gray-200 p-2"><?php echo $projet['titre_projet']; ?></td>
                        <td class="border border-gray-200 p-2"><?php echo $projet['description']; ?></td>
                        <td class="border border-gray-200 p-2"><?php echo $projet['nom_categorie']; ?></td>
                        <td class="border border-gray-200 p-2"><?php echo $projet['nom_utilisateur']; ?></td>
                        <td class="border border-gray-200 p-2">
                            
                            <button onclick="showUpdateForm(<?php echo $projet['id_projet']; ?>, '<?php echo addslashes($projet['titre_projet']); ?>', '<?php echo addslashes($projet['description']); ?>')" class="bg-yellow-500 text-white p-1 rounded">Update</button>
                            <form method="POST" action="" class="inline">
                                <input type="hidden" name="id_projet" value="<?php echo $projet['id_projet']; ?>">
                                <button type="submit" name="delete_projet" class="bg-red-600 text-white p-1 rounded">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button onclick="showAddProjectForm()" class="bg-green-500 text-white p-2 rounded mb-4">Ajouter un Projet</button>
            <div id="addProjectForm" class="hidden mt-4">
    <h3 class="text-lg font-bold">Ajouter un Projet</h3>
    <form method="POST" action="">
        <input type="text" name="titre_projet" placeholder="Titre" required class="border p-2 mb-2 w-full">
        <textarea name="description" placeholder="Description" required class="border p-2 mb-2 w-full"></textarea>
        <input type="number" name="id_categorie" placeholder="ID Catégorie" required class="border p-2 mb-2 w-full">
        <input type="number" name="id_utilisateur" placeholder="ID Utilisateur" required class="border p-2 mb-2 w-full">
        <button type="submit" name="add_projet" class="bg-blue-500 text-white p-1 rounded">Ajouter</button>
        <button type="button" onclick="hideAddProjectForm()" class="bg-gray-500 text-white p-1 rounded">Annuler</button>
    </form>
</div>
            
            <div id="updateForm" class="hidden mt-4">
                <h3 class="text-lg font-bold">Mettre à jour le projet</h3>
                <form method="POST" action="">
                    <input type="hidden" name="id_projet" id="update_id_projet">
                    <input type="text" name="titre_projet" id="update_titre_projet" placeholder="Titre" required>
                    <input type="text" name="description" id="update_description" placeholder="Description" required>
                    <button type="submit" name="update_projet" class="bg-blue-500 text-white p-1 rounded">Sauvegarder</button>
                    <button type="button" onclick="hideUpdateForm()" class="bg-gray-500 text-white p-1 rounded">Annuler</button>
                </form>
            </div>
        </div>

        <div id="utilisateurs" class="hidden">
            <h2 class="text-xl font-bold mb-4">Liste des Utilisateurs</h2>
            <table class="min-w-full border-collapse border border-gray-200 bg-white">
                <thead>
                    <tr>
                        <th class="border border-gray-200 p-2">ID Utilisateur</th>
                        <th class="border border-gray-200 p-2">Nom</th>
                        <th class="border border-gray-200 p-2">Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($utilisateurs as $utilisateur): ?>
                    <tr>
                        <td class="border border-gray-200 p-2"><?php echo $utilisateur['id_utilisateur']; ?></td>
                        <td class="border border-gray-200 p-2"><?php echo $utilisateur['nom_utilisateur']; ?></td>
                        <td class="border border-gray-200 p-2"><?php echo $utilisateur['email']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div id="freelancers" class="hidden">
            <h2 class="text-xl font-bold mb-4">Liste des Freelancers</h2>
            <table class="min-w-full border-collapse border border-gray-200 bg-white">
                <thead>
                    <tr>
                        <th class="border border-gray-200 p-2">ID Freelancer</th>
                        <th class="border border-gray-200 p-2">Nom</th>
                        <th class="border border-gray-200 p-2">Competences</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($freelances as $freelance): ?>
                    <tr>
                        <td class="border border-gray-200 p-2"><?php echo $freelance['id_freelance']; ?></td>
                        <td class="border border-gray-200 p-2"><?php echo $freelance['nom_freelance']; ?></td>
                        <td class="border border-gray-200 p-2"><?php echo $freelance['competences']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div id="categorier" class="hidden">
            <h2 class="text-xl font-bold mb-4">Liste des Catégories</h2>
            <table class="min-w-full border-collapse border border-gray-200 bg-white">
                <thead>
                    <tr>
                        <th class="border border-gray-200 p-2">ID Catégorie</th>
                        <th class="border border-gray-200 p-2">Nom</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categorier as $categorie): ?>
                    <tr>
                        <td class="border border-gray-200 p-2"><?php echo $categorie['id_categorie']; ?></td>
                        <td class="border border-gray-200 p-2"><?php echo $categorie['nom_categorie']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div id="sous_categories" class="hidden">
            <h2 class="text-xl font-bold mb-4">Liste des Sous-Catégories</h2>
            <table class="min-w-full border-collapse border border-gray-200 bg-white">
                <thead>
                    <tr>
                        <th class="border border-gray-200 p-2">ID Sous-Catégorie</th>
                        <th class="border border-gray-200 p-2">Nom</th>
                        <th class="border border-gray-200 p-2">ID Catégorie</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sous_categorier as $sous_categorie): ?>
                    <tr>
                        <td class="border border-gray-200 p-2"><?php echo $sous_categorie['id_sous_categorie']; ?></td>
                        <td class="border border-gray-200 p-2"><?php echo $sous_categorie['nom_sous_categorie']; ?></td>
                        <td class="border border-gray-200 p-2"><?php echo $sous_categorie['id_categorie']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function showSection(sectionId) {
    const sections = ['dashboard', 'offres', 'projets', 'utilisateurs', 'freelancers', 'categorier', 'sous_categories'];
    sections.forEach(section => {
        document.getElementById(section).classList.add('hidden');
    });
    document.getElementById(sectionId).classList.remove('hidden');
}

function showUpdateForm(id, titre, description) {
    document.getElementById('update_id_projet').value = id;
    document.getElementById('update_titre_projet').value = titre;
    document.getElementById('update_description').value = description;
    document.getElementById('updateForm').classList.remove('hidden');
}

function hideUpdateForm() {
    document.getElementById('updateForm').classList.add('hidden');
}

function showAddProjectForm() {
    document.getElementById('addProjectForm').classList.remove('hidden');
}

function hideAddProjectForm() {
    document.getElementById('addProjectForm').classList.add('hidden');
}
</script>
</body>
</html>