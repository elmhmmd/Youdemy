<?php
session_start();
require_once '../classes/database.php';
require_once '../classes/user.php';
require_once '../classes/course.php';
require_once '../classes/category.php';
require_once '../classes/enrollments.php';
require_once '../classes/tag.php';
require_once '../classes/teacher.php';

// Admin check
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header('Location: login.php');
    exit();
}

$userObj = new user();
$courseObj = new course();
$categoryObj = new category();
$enrollmentObj = new enrollments();
$tagObj = new tag();
$teacherObj = new teacher();

// Get all data
$users = $userObj->getAllUsersWithRoles();
$courses = $courseObj->getAllCourses();
$categories = $categoryObj->getAllCategories();
$tags = $tagObj->getAllTags();
$pendingTeachers = $teacherObj->getPendingTeachers();

// Statistics
$totalCourses = $courseObj->getTotalCourseCount();
$categoryDistribution = $courseObj->getCategoryDistribution();
$popularCourses = $enrollmentObj->getMostPopularCourses(3);
$topTeachers = $userObj->getTopTeachers(3);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Admin - Youdemy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-section { display: none; }
        .admin-section.active { display: block; }
        .truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .sidebar-link.active { background-color: #f3f4f6; color: #1f2937; }
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
                <a href="#validation-enseignants" class="sidebar-link block py-2 px-4 text-gray-700 hover:bg-gray-100 hover:text-gray-900 active" data-target="validation-enseignants">
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

        <div class="flex-1 ml-64 overflow-y-auto">
            <nav class="bg-white shadow-md">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-end h-16 items-center">
                        <div class="flex items-center space-x-4">
                            <?php if(isset($_SESSION['admin_notice'])): ?>
                                <div class="text-green-600"><?= $_SESSION['admin_notice'] ?></div>
                                <?php unset($_SESSION['admin_notice']); ?>
                            <?php endif; ?>
                            <?php if(isset($_SESSION['admin_error'])): ?>
                                <div class="text-red-600"><?= $_SESSION['admin_error'] ?></div>
                                <?php unset($_SESSION['admin_error']); ?>
                            <?php endif; ?>
                            <a href="../actions/logout.php" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                                <i class="fas fa-sign-out-alt mr-2"></i> Déconnexion
                            </a>
                        </div>
                    </div>
                </div>
            </nav>

            <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <!-- Teacher Validation Section -->
                <section id="validation-enseignants" class="admin-section bg-white shadow rounded-md p-4 mb-6 active">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold text-gray-800"><i class="fas fa-user-check mr-2"></i> Validation des Enseignants</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($pendingTeachers as $teacher): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($teacher['username']) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($teacher['email']) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <form action="../actions/accept_teacher.php" method="post" class="inline-block">
                                                <input type="hidden" name="teacher_id" value="<?= $teacher['user_id'] ?>">
                                                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mr-2">Valider</button>
                                            </form>
                                            <form action="../actions/reject_teacher.php" method="post" class="inline-block">
                                                <input type="hidden" name="teacher_id" value="<?= $teacher['user_id'] ?>">
                                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Refuser</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- User Management Section -->
                <section id="gestion-utilisateurs" class="admin-section bg-white shadow rounded-md p-4 mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold text-gray-800"><i class="fas fa-users mr-2"></i> Gestion des Utilisateurs</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rôle</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($users as $user): ?>
                                <tr data-user-id="<?= $user['user_id'] ?>">
                                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($user['username']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($user['email']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($user['role_name']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= ucfirst($user['status']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                                        <form action="../actions/toggle_user_status.php" method="POST" class="inline">
                                            <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                            <button type="submit" class="text-blue-600 hover:text-blue-900">
                                                <?= $user['status'] === 'suspended' ? 'Activer' : 'Suspendre' ?>
                                            </button>
                                        </form>
                                        <form action="../actions/delete_user.php" method="POST" class="inline">
                                            <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-900">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Course Management Section -->
                <section id="gestion-cours" class="admin-section bg-white shadow rounded-md p-4 mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold text-gray-800"><i class="fas fa-book mr-2"></i> Gestion des Cours</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Titre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catégorie</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Enseignant</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap truncate"><?= htmlspecialchars($course->getTitle()) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($categoryObj->getCategoryNameById($course->getCategoryId())) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars((new teacher())->getTeacherNameById($course->getTeacherId())) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <form action="../actions/delete_course.php" method="POST" class="inline">
                                            <input type="hidden" name="course_id" value="<?= $course->getCourseId() ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-900">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Category Management Section -->
                <section id="gestion-categories" class="admin-section bg-white shadow rounded-md p-4 mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold text-gray-800"><i class="fas fa-tags mr-2"></i> Gestion des Catégories</h3>
                        <button id="addCategoryButton" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">
                            <i class="fas fa-plus mr-2"></i> Ajouter
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($categories as $category): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($category->getCategoryName()) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <form action="../actions/delete_category.php" method="POST" class="inline">
                                                <input type="hidden" name="category_id" value="<?= $category->getCategoryId() ?>">
                                                <button type="submit" class="text-red-600 hover:text-red-900">Supprimer</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Tag Management Section -->
                <section id="gestion-tags" class="admin-section bg-white shadow rounded-md p-4 mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold text-gray-800"><i class="fas fa-hashtag mr-2"></i> Gestion des Tags</h3>
                        <button id="addTagButton" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">
                            <i class="fas fa-plus mr-2"></i> Ajouter
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($tags as $tag): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($tag['tag_name']) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <form action="../actions/delete_tag.php" method="POST" class="inline">
                                                <input type="hidden" name="tag_id" value="<?= $tag['tag_id'] ?>">
                                                <button type="submit" class="text-red-600 hover:text-red-900">Supprimer</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Statistics Section -->
                <section id="statistiques-globales" class="admin-section bg-white shadow rounded-md p-4 mb-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4"><i class="fas fa-chart-bar mr-2"></i> Statistiques Globales</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-gray-50 rounded-md p-4">
                            <dt class="text-sm font-medium text-gray-500">Total Cours</dt>
                            <dd class="text-2xl font-semibold"><?= $totalCourses ?></dd>
                        </div>
                        
                        <!-- Category Distribution -->
                        <div class="bg-gray-50 rounded-md p-4">
                            <dt class="text-sm font-medium text-gray-500">Répartition par Catégorie</dt>
                            <dd class="mt-2 space-y-1">
                                <?php foreach ($categoryDistribution as $category): ?>
                                    <div class="flex justify-between">
                                        <span><?= htmlspecialchars($category['category_name']) ?></span>
                                        <span><?= $category['course_count'] ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </dd>
                        </div>

                        <!-- Popular Courses -->
                        <div class="bg-gray-50 rounded-md p-4">
                            <dt class="text-sm font-medium text-gray-500">Cours Populaires</dt>
                            <dd class="mt-2 space-y-1">
                                <?php foreach ($popularCourses as $course): ?>
                                    <div class="truncate"><?= htmlspecialchars($course['title']) ?></div>
                                <?php endforeach; ?>
                            </dd>
                        </div>

                        <!-- Top Teachers -->
                        <div class="bg-gray-50 rounded-md p-4">
                            <dt class="text-sm font-medium text-gray-500">Top Enseignants</dt>
                            <dd class="mt-2 space-y-1">
                                <?php foreach ($topTeachers as $teacher): ?>
                                    <div class="truncate"><?= htmlspecialchars($teacher['username']) ?></div>
                                <?php endforeach; ?>
                            </dd>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <script>
        // Section switching
        document.querySelectorAll('.sidebar-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const target = this.dataset.target;
                
                document.querySelectorAll('.admin-section').forEach(section => {
                    section.classList.remove('active');
                });
                
                document.querySelectorAll('.sidebar-link').forEach(link => {
                    link.classList.remove('active');
                });

                document.getElementById(target).classList.add('active');
                this.classList.add('active');
            });
        });

        // Auto-scroll to hash on page load
        window.addEventListener('load', () => {
            if(window.location.hash) {
                const targetSection = document.querySelector(window.location.hash);
                if(targetSection) {
                    targetSection.classList.add('active');
                    document.querySelector(`[data-target="${window.location.hash}"]`).classList.add('active');
                }
            }
        });
    </script>
</body>
</html>