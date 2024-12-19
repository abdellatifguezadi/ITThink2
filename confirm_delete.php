<?php
session_start();
require 'db_connection.php'; 

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: login.php'); 
    exit();
}

// Récupération des identifiants
$id = $_POST['id_projet'] ?? $_POST['id_utilisateur'] ?? $_POST['id_freelance'] ?? $_POST['id_categorie'] ?? $_POST['id_sous_categorie'] ?? $_POST['id_temoignages'] ?? null;
$type = $_POST['type'] ?? null;

if (!$id || !$type) {
    header('Location: admin_page.php');
    exit();
}

// Récupération des informations selon le type
try {
    switch ($type) {
        case 'projet':
            $stmt = $pdo->prepare("SELECT * FROM projets WHERE id_projet = ?");
            $stmt->execute([$id]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            break;

        case 'utilisateur':
            $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id_utilisateur = ?");
            $stmt->execute([$id]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            break;

        case 'freelance':
            $stmt = $pdo->prepare("SELECT * FROM freelances WHERE id_freelance = ?");
            $stmt->execute([$id]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            break;

        case 'categorie':
            $stmt = $pdo->prepare("SELECT * FROM categories WHERE id_categorie = ?");
            $stmt->execute([$id]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            break;

        case 'sous_categorie':
            $stmt = $pdo->prepare("SELECT * FROM sous_categories WHERE id_sous_categorie = ?");
            $stmt->execute([$id]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            break;
        
        case 'temoignages':
            $stmt = $pdo->prepare("SELECT * FROM temoignages WHERE id_temoignage = ?");
            $stmt->execute([$id]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            break;

        default:
            header('Location: admin_page.php');
            exit();
    }

    if (!$item) {
        echo "Élément non trouvé.";
        exit();
    }
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des données : " . $e->getMessage();
    exit();
}

// Traitement de la suppression après confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    try {
        switch ($type) {
            case 'projet':
                $stmt = $pdo->prepare("DELETE FROM projets WHERE id_projet = ?");
                break;
            case 'utilisateur':
                $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id_utilisateur = ?");
                break;
            case 'freelance':
                $stmt = $pdo->prepare("DELETE FROM freelances WHERE id_freelance = ?");
                break;
            case 'categorie':
                $stmt = $pdo->prepare("DELETE FROM categories WHERE id_categorie = ?");
                break;
            case 'sous_categorie':
                $stmt = $pdo->prepare("DELETE FROM sous_categories WHERE id_sous_categorie = ?");
                break;
            case 'temoignages': 
                $stmt = $pdo->prepare("DELETE FROM temoignages WHERE id_temoignage = ?");
        }

        $stmt->execute([$id]);
        header('Location: admin_page.php');
        exit();
    } catch (PDOException $e) {
        echo "Erreur lors de la suppression : " . $e->getMessage();
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation de Suppression</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Confirmation de Suppression</h1>

        <?php switch($type): 
            case 'projet': ?>
                <p class="mb-4 text-gray-600">Êtes-vous sûr de vouloir supprimer le projet suivant ?</p>
                <div class="mb-6">
                    <p class="mb-2"><strong>Titre :</strong> <?php echo htmlspecialchars($item['titre_projet']); ?></p>
                    <p><strong>Description :</strong> <?php echo htmlspecialchars($item['description']); ?></p>
                </div>
                <?php break; ?>

            <?php case 'utilisateur': ?>
                <p class="mb-4 text-gray-600">Êtes-vous sûr de vouloir supprimer l'utilisateur suivant ?</p>
                <div class="mb-6">
                    <p class="mb-2"><strong>Nom :</strong> <?php echo htmlspecialchars($item['nom_utilisateur']); ?></p>
                    <p><strong>Email :</strong> <?php echo htmlspecialchars($item['email']); ?></p>
                </div>
                <?php break; ?>

            <?php case 'freelance': ?>
                <p class="mb-4 text-gray-600">Êtes-vous sûr de vouloir supprimer le freelance suivant ?</p>
                <div class="mb-6">
                    <p class="mb-2"><strong>Nom :</strong> <?php echo htmlspecialchars($item['nom_freelance']); ?></p>
                    <p><strong>Compétences :</strong> <?php echo htmlspecialchars($item['competences']); ?></p>
                </div>
                <?php break; ?>

            <?php case 'categorie': ?>
                <p class="mb-4 text-gray-600">Êtes-vous sûr de vouloir supprimer la catégorie suivante ?</p>
                <div class="mb-6">
                    <p><strong>Nom :</strong> <?php echo htmlspecialchars($item['nom_categorie']); ?></p>
                </div>
                <?php break; ?>

            <?php case 'sous_categorie': ?>
                <p class="mb-4 text-gray-600">Êtes-vous sûr de vouloir supprimer la sous-catégorie suivante ?</p>
                <div class="mb-6">
                    <p><strong>Nom :</strong> <?php echo htmlspecialchars($item['nom_sous_categorie']); ?></p>
                </div>
                <?php break; ?>

            <?php case 'temoignages' : ?>
                <p class="mb-4 text-gray-600">Êtes-vous sûr de vouloir supprimer le témoignage suivant ?</p>
                <div class="mb-6">
                    <p><strong> commantaire : </strong> <?php echo htmlspecialchars($item['commentaire']); ?> </p>    
                </div>    
                <?php break; ?>

        <?php endswitch; ?>

        <form method="POST" action="" class="flex justify-between">
            <?php switch($type): 
                case 'projet': ?>
                    <input type="hidden" name="id_projet" value="<?php echo htmlspecialchars($id); ?>">
                    <?php break; ?>
                <?php case 'utilisateur': ?>
                    <input type="hidden" name="id_utilisateur" value="<?php echo htmlspecialchars($id); ?>">
                    <?php break; ?>
                <?php case 'freelance': ?>
                    <input type="hidden" name="id_freelance" value="<?php echo htmlspecialchars($id); ?>">
                    <?php break; ?>
                <?php case 'categorie': ?>
                    <input type="hidden" name="id_categorie" value="<?php echo htmlspecialchars($id); ?>">
                    <?php break; ?>
                <?php case 'sous_categorie': ?>
                    <input type="hidden" name="id_sous_categorie" value="<?php echo htmlspecialchars($id); ?>">
                    <?php break; ?>
                <?php case 'temoignages' : ?>
                    <input type="hidden" name="id_temoignages" value="<?php echo htmlspecialchars($id); ?>">
                    <?php break; ?>
            <?php endswitch; ?>
            
            <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>">
            
            <button type="submit" name="confirm_delete" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-200">
                Confirmer la suppression
            </button>
            
            <a href="admin_page.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-200">
                Annuler
            </a>
        </form>
    </div>
</body>
</html>


