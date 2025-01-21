<?php

session_start();

require_once '../classes/database.php';
require_once '../classes/user.php';
require_once '../classes/teacher.php';
require_once '../classes/category.php';
require_once '../classes/tag.php';

$teacher = new teacher();
$category = new category();
$tag = new tag();

$categories = $category->getAllCategories();
$pendingteachers = $teacher->getPendingTeachers();
$tags = $tag->getAllTags();

if (!isset($_SESSION['user_id'])) {
    header('Location: ./login.php');
    exit();
}

if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {

    if ($_SESSION['role_id'] == 2) {
        header('Location: ./enseignant_dashboard.php');
        exit();
    } elseif ($_SESSION['role_id'] == 3) {
        header('Location: ./etudiant_page.php');
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Admin - Youdemy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .admin-section {
            display: none;
        }
        .admin-section.active {
            display: block;
        }
        #gestion-utilisateurs td button {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }
        .truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-md fixed h-full">
            <div class="p-4">
                <div class="flex items-center">
                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                    </svg>
                    <span class="ml-2 text-lg font-bold text-gray-900">Youdemy Admin</span>
                </div>
            </div>
            <nav class="mt-6">
                <a href="#validation-enseignants" class="sidebar-link block py-2 px-4 text-gray-700 hover:bg-gray-100 hover:text-gray-900" data-target="validation-enseignants">
                    <i class="fas fa-user-check mr-2"></i> Validation Enseignants
                </a>
                <a href="#gestion-utilisateurs" class="sidebar-link block py-2 px-4 text-gray-700 hover:bg-gray-100 hover:text-gray-900" data-target="gestion-utilisateurs">
                    <i class="fas fa-users mr-2"></i> Gestion Utilisateurs
                </a>
                <a href="#gestion-cours" class="sidebar-link block py-2 px-4 text-gray-700 hover:bg-gray-100 hover:text-gray-900" data-target="gestion-cours">
                    <i class="fas fa-book mr-2"></i> Gestion Cours
                </a>
                <a href="#gestion-categories" class="sidebar-link block py-2 px-4 text-gray-700 hover:bg-gray-100 hover:text-gray-900" data-target="gestion-categories">
                    <i class="fas fa-tags mr-2"></i> Gestion Catégories
                </a>
                <a href="#gestion-tags" class="sidebar-link block py-2 px-4 text-gray-700 hover:bg-gray-100 hover:text-gray-900" data-target="gestion-tags">
                    <i class="fas fa-hashtag mr-2"></i> Gestion Tags
                </a>
                <a href="#statistiques-globales" class="sidebar-link block py-2 px-4 text-gray-700 hover:bg-gray-100 hover:text-gray-900" data-target="statistiques-globales">
                    <i class="fas fa-chart-bar mr-2"></i> Statistiques Globales
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 ml-64 overflow-y-auto">
            <nav class="bg-white shadow-md">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-end h-16 items-center">
                        <div class="flex items-center space-x-4">
                            <a href="../actions/logout.php" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                                <i class="fas fa-sign-out-alt mr-2"></i> Déconnexion
                            </a>
                        </div>
                    </div>
                </div>
            </nav>

            <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">Tableau de Bord Administrateur</h2>

                <!-- Validation des Enseignants -->
                <section id="validation-enseignants" class="admin-section bg-white shadow rounded-md p-4 mb-6 active">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4"><i class="fas fa-user-check mr-2"></i> Validation des Enseignants</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($pendingteachers as $teacher): ?>
                                    <tr id="teacher-<?php echo $teacher['user_id']; ?>">
                                        <td class="px-6 py-4 whitespace-nowrap align-middle"><?php echo htmlspecialchars($teacher['username']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap align-middle"><?php echo htmlspecialchars($teacher['email']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium align-middle">
                                        <form action="../actions/accept_teacher.php" method="post" class="inline-block">
                                            <input type="hidden" name="teacher_id" value="<?php echo $teacher['user_id']; ?>">
                                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mr-2">Valider</button>
                                        </form>
                                        <form action="../actions/reject_teacher.php" method="post" class="inline-block">
                                            <input type="hidden" name="teacher_id" value="<?php echo $teacher['user_id']; ?>">
                                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Refuser</button>
                                        </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Gestion des Utilisateurs -->
                <section id="gestion-utilisateurs" class="admin-section bg-white shadow rounded-md p-4 mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold text-gray-800"><i class="fas fa-users mr-2"></i> Gestion des Utilisateurs</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôle</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr data-user-id="1" data-user-status="actif">
                                    <td class="px-6 py-4 whitespace-nowrap align-middle">Pierre Dubois</td>
                                    <td class="px-6 py-4 whitespace-nowrap align-middle">pierre.dubois@example.com</td>
                                    <td class="px-6 py-4 whitespace-nowrap align-middle">Étudiant</td>
                                    <td class="px-6 py-4 whitespace-nowrap align-middle">Actif</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium align-middle">
                                        <button class="text-red-600 hover:text-red-900 mr-2 delete-user-btn">Supprimer</button>
                                        <button class="text-blue-600 hover:text-blue-900 toggle-user-status-btn">Suspendre</button>
                                    </td>
                                </tr>
                                <tr data-user-id="2" data-user-status="actif">
                                    <td class="px-6 py-4 whitespace-nowrap align-middle">Julie Lefevre</td>
                                    <td class="px-6 py-4 whitespace-nowrap align-middle">julie.lefevre@example.com</td>
                                    <td class="px-6 py-4 whitespace-nowrap align-middle">Enseignant</td>
                                    <td class="px-6 py-4 whitespace-nowrap align-middle">Actif</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium align-middle">
                                        <button class="text-red-600 hover:text-red-900 mr-2 delete-user-btn">Supprimer</button>
                                        <button class="text-blue-600 hover:text-blue-900 toggle-user-status-btn">Suspendre</button>
                                    </td>
                                </tr>
                                <tr data-user-id="3" data-user-status="suspendu">
                                    <td class="px-6 py-4 whitespace-nowrap align-middle">Martin Jean</td>
                                    <td class="px-6 py-4 whitespace-nowrap align-middle">martin.jean@example.com</td>
                                    <td class="px-6 py-4 whitespace-nowrap align-middle">Étudiant</td>
                                    <td class="px-6 py-4 whitespace-nowrap align-middle">Suspendu</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium align-middle">
                                        <button class="text-red-600 hover:text-red-900 mr-2 delete-user-btn">Supprimer</button>
                                        <button class="text-green-600 hover:text-green-900 toggle-user-status-btn">Activer</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Gestion des Cours -->
                <section id="gestion-cours" class="admin-section bg-white shadow rounded-md p-4 mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold text-gray-800"><i class="fas fa-book mr-2"></i> Gestion des Cours</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titre</th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contenu</th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tags</th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie</th>
                                    <th scope="col" class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-3 py-4 whitespace-nowrap align-middle truncate">Introduction à JavaScript</td>
                                    <td class="px-3 py-4 whitespace-nowrap align-middle truncate">Un cours pour débuter en JavaScript et apprendre les bases de ce langage de programmation populaire...</td>
                                    <td class="px-3 py-4 whitespace-nowrap align-middle">Vidéo</td>
                                    <td class="px-3 py-4 whitespace-nowrap align-middle truncate">javascript, débutant, programmation web</td>
                                    <td class="px-3 py-4 whitespace-nowrap align-middle">Programmation</td>
                                    <td class="px-3 py-4 whitespace-nowrap text-right text-sm font-medium align-middle">
                                        <button class="text-red-600 hover:text-red-900 delete-course-btn">Supprimer</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-3 py-4 whitespace-nowrap align-middle truncate">Les Fondamentaux du Marketing Digital</td>
                                    <td class="px-3 py-4 whitespace-nowrap align-middle truncate">Découvrez les stratégies clés du marketing digital pour développer votre présence en ligne et atteindre vos objectifs...</td>
                                    <td class="px-3 py-4 whitespace-nowrap align-middle">Document</td>
                                    <td class="px-3 py-4 whitespace-nowrap align-middle truncate">marketing digital, seo, réseaux sociaux</td>
                                    <td class="px-3 py-4 whitespace-nowrap align-middle">Marketing</td>
                                    <td class="px-3 py-4 whitespace-nowrap text-right text-sm font-medium align-middle">
                                        <button class="text-red-600 hover:text-red-900 delete-course-btn">Supprimer</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Gestion des Catégories -->
<section id="gestion-categories" class="admin-section bg-white shadow rounded-md p-4 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-semibold text-gray-800"><i class="fas fa-tags mr-2"></i> Gestion des Catégories</h3>
        <button id="addCategoryButton" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            <i class="fas fa-plus mr-2"></i> Ajouter une catégorie
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($categories as $category): ?>
                        <tr data-category-id="<?php echo $category->getCategoryId(); ?>">
                            <td class="px-6 py-4 whitespace-nowrap align-middle"><?php echo htmlspecialchars($category->getCategoryName()); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium align-middle">
                                <form action="../actions/delete_category.php" method="post" class="inline-block">
                                    <input type="hidden" name="category_id" value="<?php echo $category->getCategoryId(); ?>">
                                    <button type="submit" class="text-red-600 hover:text-red-900 delete-category-btn">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

                <!-- Gestion des Tags -->
        <section id="gestion-tags" class="admin-section bg-white shadow rounded-md p-4 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-gray-800"><i class="fas fa-hashtag mr-2"></i> Gestion des Tags</h3>
                    <button id="addBulkTagsButton" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <i class="fas fa-plus-circle mr-2"></i> Insertion en masse
                    </button>
                    </div>
                <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($tags as $tag): ?>
                            <tr data-tag-id="<?php echo $tag['tag_id']; ?>">
                                <td class="px-6 py-4 whitespace-nowrap align-middle"><?php echo htmlspecialchars($tag['tag_name']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium align-middle">
                                    <form action="../actions/delete_tag.php" method="post" class="inline-block">
                                        <input type="hidden" name="tag_id" value="<?php echo $tag['tag_id']; ?>">
                                        <button type="submit" class="text-red-600 hover:text-red-900 delete-tag-btn">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

                <!-- Statistiques Globales -->
                <section id="statistiques-globales" class="admin-section bg-white shadow rounded-md p-4 mb-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4"><i class="fas fa-chart-bar mr-2"></i> Statistiques Globales</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="bg-gray-50 rounded-md p-4">
                            <dt class="text-sm font-medium text-gray-500">Nombre total de cours</dt>
                            <dd class="text-lg font-semibold text-gray-900">25</dd>
                        </div>
                        <div class="bg-gray-50 rounded-md p-4">
                            <dt class="text-sm font-medium text-gray-500">Répartition par catégorie</dt>
                            <dd class="text-lg font-semibold text-gray-900">
                                <ul class="list-disc list-inside">
                                    <li>Programmation: 10</li>
                                    <li>Marketing: 8</li>
                                    <li>Design: 7</li>
                                </ul>
                            </dd>
                        </div>
                        <div class="bg-gray-50 rounded-md p-4">
                            <dt class="text-sm font-medium text-gray-500">Cours le plus populaire</dt>
                            <dd class="text-lg font-semibold text-gray-900">Introduction à Python (58 étudiants)</dd>
                        </div>
                        <div class="bg-gray-50 rounded-md p-4">
                            <dt class="text-sm font-medium text-gray-500">Top 3 des enseignants</dt>
                            <dd class="text-lg font-semibold text-gray-900">
                                <ol class="list-decimal list-inside">
                                    <li>Julie Lefevre (8 cours)</li>
                                    <li>Pierre Martin (7 cours)</li>
                                    <li>Sophie Dubois (6 cours)</li>
                                </ol>
                            </dd>
                        </div>
                    </div>
                </section>

                              <!-- Add Category Modal -->
                              <div id="addCategoryModal" class="hidden fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span>
                        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title-add-category">
                                            Ajouter une catégorie
                                        </h3>
                                        <div class="mt-2">
                                            <form id="addCategoryForm" action="../actions/add_category.php" method="POST">
                                                <div class="mb-4">
                                                    <label for="categoryName" class="block text-gray-700 text-sm font-bold mb-2">Nom de la catégorie</label>
                                                    <input type="text" id="categoryName" name="categoryName" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Nom de la catégorie">
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" form="addCategoryForm" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto">
                                Ajouter
                            </button>
                                <button id="cancelAddCategory" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto">
                                    Annuler
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add Bulk Tags Modal -->
<div id="addBulkTagsModal" class="hidden fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title-add-bulk-tags">
                            Insertion en masse de tags
                        </h3>
                        <div class="mt-2">
                            <form id="addBulkTagsForm" action="../actions/add_tags.php" method="POST">
                                <div class="mb-4">
                                    <label for="tagsInput" class="block text-gray-700 text-sm font-bold mb-2">Tags (séparés par des virgules)</label>
                                    <textarea id="tagsInput" name="tagString" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="tag1, tag2, tag3"></textarea>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="submit" form="addBulkTagsForm" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto">
                    Ajouter
                </button>
                <button id="cancelAddBulkTags" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto">
                    Annuler
                </button>
            </div>
        </div>
    </div>
</div>

            </main>
        </div>
    </div>

    <script>
        const sidebarLinks = document.querySelectorAll('.sidebar-link');
        const adminSections = document.querySelectorAll('.admin-section');

        function showSection(targetId) {
            adminSections.forEach(section => {
                section.classList.remove('active');
            });
            const targetSection = document.getElementById(targetId);
            if (targetSection) {
                targetSection.classList.add('active');
            }
        }

        if (adminSections.length > 0) {
            adminSections[0].classList.add('active');
        }

        sidebarLinks.forEach(link => {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                const target = this.getAttribute('data-target');
                showSection(target);
            });
        });

        const addCategoryButton = document.getElementById('addCategoryButton');
        const addCategoryModal = document.getElementById('addCategoryModal');
        const cancelAddCategoryButton = document.getElementById('cancelAddCategory');

        const addBulkTagsButton = document.getElementById('addBulkTagsButton');
        const addBulkTagsModal = document.getElementById('addBulkTagsModal');
        const cancelAddBulkTagsButton = document.getElementById('cancelAddBulkTags');

        if (addCategoryButton) {
            addCategoryButton.addEventListener('click', () => {
                addCategoryModal.classList.remove('hidden');
            });
        }

        if (cancelAddCategoryButton) {
            cancelAddCategoryButton.addEventListener('click', () => {
                addCategoryModal.classList.add('hidden');
            });
        }

        if (addBulkTagsButton) {
            addBulkTagsButton.addEventListener('click', () => {
                addBulkTagsModal.classList.remove('hidden');
            });
        }

        if (cancelAddBulkTagsButton) {
            cancelAddBulkTagsButton.addEventListener('click', () => {
                addBulkTagsModal.classList.add('hidden');
            });
        }

        // User Management Actions
        const userTableBody = document.querySelector('#gestion-utilisateurs tbody');
        if (userTableBody) {
            userTableBody.addEventListener('click', function(event) {
                const target = event.target;
                if (target.classList.contains('delete-user-btn')) {
                    const row = target.closest('tr');
                    if (row) {
                        const userId = row.dataset.userId;
                        console.log(`Supprimer l'utilisateur avec l'ID: ${userId}`);
                        // The actual implementation will be done on the backend (PHP)
                        row.remove(); // For demonstration purposes, just remove the row
                    }
                } else if (target.classList.contains('toggle-user-status-btn')) {
                    const row = target.closest('tr');
                    if (row) {
                        const userId = row.dataset.userId;
                        let userStatus = row.dataset.userStatus;
                        const button = target;

                        if (userStatus === 'actif') {
                            userStatus = 'suspendu';
                            button.textContent = 'Activer';
                            button.classList.remove('text-blue-600', 'hover:text-blue-900');
                            button.classList.add('text-green-600',
                            'hover:text-green-900');
                        } else {
                            userStatus = 'actif';
                            button.textContent = 'Suspendre';
                            button.classList.remove('text-green-600', 'hover:text-green-900');
                            button.classList.add('text-blue-600', 'hover:text-blue-900');
                        }
                        row.dataset.userStatus = userStatus;
                        // In a real application, you would make an API call to update the user's status via PHP
                        console.log(`Changer le statut de l'utilisateur avec l'ID: ${userId} à: ${userStatus}`);
                        // Optionally update the status displayed in the table cell
                        const statusCell = row.querySelector('td:nth-child(4)');
                        if (statusCell) {
                            statusCell.textContent = userStatus.charAt(0).toUpperCase() + userStatus.slice(1);
                        }
                    }
                }
            });
        }

        // Course Management Actions
        const courseTableBody = document.querySelector('#gestion-cours tbody');
        if (courseTableBody) {
            courseTableBody.addEventListener('click', function(event) {
                const target = event.target;
                if (target.classList.contains('delete-course-btn')) {
                    const row = target.closest('tr');
                    if (row) {
                        const courseTitle = row.querySelector('td:first-child').textContent;
                        console.log(`Supprimer le cours: ${courseTitle}`);
                        // The actual deletion will be handled by the backend (PHP)
                        row.remove(); // For demonstration
                    }
                }
            });
        }
    </script>
</body>
</html>