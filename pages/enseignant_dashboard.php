<?php

session_start();

require_once '../classes/database.php';
require_once '../classes/user.php';
require_once '../classes/course.php';
require_once '../classes/category.php';

$course = new course();
$categoryobj = new category();
$categories = $categoryobj->getAllCategories();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 2) {

    if ($_SESSION['role_id'] == 1) {
        header('Location: ./admin_dashboard.php');
        exit();
    } elseif ($_SESSION['role_id'] == 3) {
        header('Location: ./etudiant_page.php');
        exit();
    } else {
        header('Location: ./login.php');
        exit();
    }
}
$teacherId = $_SESSION['user_id'];
$courses = $course->getCoursesByTeacherId($teacherId);
$courseCount = count($courses); 

?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Enseignant - Youdemy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="bg-gradient-to-b from-blue-50 to-white min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 flex items-center">
                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                    </svg>
                    <span class="ml-2 text-2xl font-bold text-gray-900">Youdemy</span>
                </div>
                <div class="flex items-center space-x-4">
                    <button id="addCourseButton" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <i class="fas fa-plus mr-2"></i> Ajouter un cours
                    </button>
                    <a href="../actions/logout.php" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                        Déconnexion
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Dashboard Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <h2 class="text-2xl font-semibold text-gray-900 mb-4">Tableau de Bord Enseignant</h2>

                <!-- Course Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <div class="bg-white shadow rounded-md p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-500 text-white rounded-md p-2">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="ml-4">
                        <dt class="text-sm font-medium text-gray-500">Étudiants Inscrits</dt>
                        <dd class="text-lg font-semibold text-gray-900">567</dd>
                    </div>
                </div>
            </div>
            <div class="bg-white shadow rounded-md p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-500 text-white rounded-md p-2">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="ml-4">
                        <dt class="text-sm font-medium text-gray-500">Nombre de Cours</dt>
                        <dd class="text-lg font-semibold text-gray-900"><?= $courseCount ?></dd>
                    </div>
                </div>
            </div>
            <div class="bg-white shadow rounded-md p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-500 text-white rounded-md p-2">
                        <i class="fas fa-video"></i>
                    </div>
                    <div class="ml-4">
                        <dt class="text-sm font-medium text-gray-500">Vidéos Totales</dt>
                        <dd class="text-lg font-semibold text-gray-900">85</dd>
                    </div>
                </div>
            </div>
        </div>

                <!-- Course Table -->
                <div class="bg-white shadow rounded-md overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Titre
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Description
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Catégorie
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Inscrits
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($courses)): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap" colspan="5">
                                <div class="text-sm text-gray-500 text-center">Aucun cours trouvé.</div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($courses as $courseobj): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($courseobj->getTitle()) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-normal max-w-sm overflow-hidden text-ellipsis">
                                    <div class="text-sm text-gray-500"><?= htmlspecialchars($courseobj->getDescription()) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <?=htmlspecialchars($categoryobj->getCategoryNameById($courseobj->getCategoryId()));?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <!-- You'll need to implement a way to count enrolled students for each course -->
                                    0
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button data-course-id="<?= $courseobj->getCourseId() ?>" class="consultInscriptionsButton text-indigo-600 hover:text-indigo-900 mr-2">Consulter</button>
                                    <button data-course-id="<?= $courseobj->getCourseId() ?>" class="modifyCourseButton text-green-600 hover:text-green-900 mr-2">Modifier</button>
                                    <button data-course-id="<?= $courseobj->getCourseId() ?>" class="deleteCourseButton text-red-600 hover:text-red-900">Supprimer</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- Add Course Modal -->
        <div id="addCourseModal" class="hidden fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Ajouter un nouveau cours
                                </h3>
                                <div class="mt-2">
                                    <form id="addCourseForm" action="../actions/add_course.php" method="POST" enctype="multipart/form-data">
                                        <div class="mb-4">
                                            <label for="courseTitle" class="block text-gray-700 text-sm font-bold mb-2">Titre</label>
                                            <input type="text" id="courseTitle" name="title" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Titre du cours">
                                        </div>
                                        <div class="mb-4">
                                            <label for="courseDescription" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                                            <textarea id="courseDescription" name="description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Description du cours"></textarea>
                                        </div>
                                        <div class="mb-4">
                                            <label class="block text-gray-700 text-sm font-bold mb-2">Contenu</label>
                                            <div class="flex items-center mb-2">
                                                <input type="radio" id="contentTypeVideo" name="content_type" value="video" class="mr-2">
                                                <label for="contentTypeVideo" class="text-gray-700 text-sm">Vidéo</label>
                                                <input type="radio" id="contentTypeDocument" name="content_type" value="document" class="ml-4 mr-2">
                                                <label for="contentTypeDocument" class="text-gray-700 text-sm">Document</label>
                                            </div>
                                            <div id="videoContentInput" class="hidden">
                                                <input type="url" id="courseVideoUrl" name="content_url" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Lien de la vidéo">
                                            </div>
                                            <div id="documentContentInput" class="hidden">
                                                <input type="file" id="courseDocument" name="content_document" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            </div>
                                        </div>
                                        <div class="mb-4">
                                            <label for="courseCategory" class="block text-gray-700 text-sm font-bold mb-2">Catégorie</label>
                                            <select id="courseCategory" name="category_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            <?php foreach ($categories as $cat): ?>
                                                <option value="<?= $cat->getCategoryId() ?>"><?= htmlspecialchars($cat->getCategoryName()) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" onclick="document.getElementById('addCourseForm').submit();" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Enregistrer
                        </button>
                        <button id="cancelAddCourse" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto">
                            Annuler
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Consult Inscriptions Modal -->
        <div id="consultInscriptionsModal" class="hidden fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title-inscriptions">
                                    Inscriptions pour <span id="courseTitleInscriptions"></span>
                                </h3>
                                <div class="mt-2">
                                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Nom
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Email
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody id="inscriptionsTableBody" class="bg-white divide-y divide-gray-200">
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm font-medium text-gray-900">John Doe</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm text-gray-500">john.doe@example.com</div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm font-medium text-gray-900">Jane Smith</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm text-gray-500">jane.smith@example.com</div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" id="closeInscriptionsModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto">
                            Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div id="modifyCourseModal" class="hidden fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title-modify">
                                    Modifier le cours
                                </h3>
                                <div class="mt-2">
                                    <form id="modifyCourseForm">
                                        <input type="hidden" id="modifyCourseId">
                                        <div class="mb-4">
                                            <label for="modifyCourseTitle" class="block text-gray-700 text-sm font-bold mb-2">Titre</label>
                                            <input type="text" id="modifyCourseTitle" name="modifyCourseTitle" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Titre du cours">
                                        </div>
                                        <div class="mb-4">
                                            <label for="modifyCourseDescription" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                                            <textarea id="modifyCourseDescription" name="modifyCourseDescription" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Description du cours"></textarea>
                                        </div>
                                        <div class="mb-4">
                                            <label class="block text-gray-700 text-sm font-bold mb-2">Contenu</label>
                                            <div class="flex items-center mb-2">
                                                <input type="radio" id="modifyContentTypeVideo" name="modifyContentType" value="video" class="mr-2">
                                                <label for="modifyContentTypeVideo" class="text-gray-700 text-sm">Vidéo</label>
                                                <input type="radio" id="modifyContentTypeDocument" name="modifyContentType" value="document" class="ml-4 mr-2">
                                                <label for="modifyContentTypeDocument" class="text-gray-700 text-sm">Document</label>
                                            </div>
                                            <div id="modifyVideoContentInput" class="hidden">
                                                <input type="url" id="modifyCourseVideoUrl" name="modifyCourseVideoUrl" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Lien de la vidéo">
                                            </div>
                                            <div id="modifyDocumentContentInput" class="hidden">
                                                <input type="file" id="modifyCourseDocument" name="modifyCourseDocument" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            </div>
                                        </div>
                                        <div class="mb-4">
                                            <label for="modifyCourseTags" class="block text-gray-700 text-sm font-bold mb-2">Tags</label>
                                            <input type="text" id="modifyCourseTags" name="modifyCourseTags" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Mots-clés séparés par des virgules">
                                        </div>
                                        <div class="mb-4">
                                            <label for="modifyCourseCategory" class="block text-gray-700 text-sm font-bold mb-2">Catégorie</label>
                                            <input type="text" id="modifyCourseCategory" name="modifyCourseCategory" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Catégorie du cours">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto">
                            Modifier
                        </button>
                        <button id="cancelModifyCourse" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto">
                            Annuler
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div id="deleteConfirmationModal" class="hidden fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title-delete">
                                    Supprimer le cours
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Êtes-vous sûr de vouloir supprimer ce cours ? Cette action est irréversible.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto">
                            Supprimer
                        </button>
                        <button id="cancelDeleteCourse" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto">
                            Annuler
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <script>
        const addCourseButton = document.getElementById('addCourseButton');
        const addCourseModal = document.getElementById('addCourseModal');
        const cancelAddCourseButton = document.getElementById('cancelAddCourse');

        const consultInscriptionsButtons = document.querySelectorAll('.consultInscriptionsButton');
        const consultInscriptionsModal = document.getElementById('consultInscriptionsModal');
        const closeInscriptionsModalButton = document.getElementById('closeInscriptionsModal');
        const courseTitleInscriptions = document.getElementById('courseTitleInscriptions');
        const inscriptionsTableBody = document.getElementById('inscriptionsTableBody');

        const modifyCourseButtons = document.querySelectorAll('.modifyCourseButton');
        const modifyCourseModal = document.getElementById('modifyCourseModal');
        const cancelModifyCourseButton = document.getElementById('cancelModifyCourse');

        const deleteCourseButtons = document.querySelectorAll('.deleteCourseButton');
        const deleteConfirmationModal = document.getElementById('deleteConfirmationModal');
        const cancelDeleteCourseButton = document.getElementById('cancelDeleteCourse');

        const contentTypeVideoRadio = document.getElementById('contentTypeVideo');
        const contentTypeDocumentRadio = document.getElementById('contentTypeDocument');
        const videoContentInput = document.getElementById('videoContentInput');
        const documentContentInput = document.getElementById('documentContentInput');

        const modifyContentTypeVideoRadio = document.getElementById('modifyContentTypeVideo');
        const modifyContentTypeDocumentRadio = document.getElementById('modifyContentTypeDocument');
        const modifyVideoContentInput = document.getElementById('modifyVideoContentInput');
        const modifyDocumentContentInput = document.getElementById('modifyDocumentContentInput');

        addCourseButton.addEventListener('click', () => {
            addCourseModal.classList.remove('hidden');
        });

        cancelAddCourseButton.addEventListener('click', () => {
            addCourseModal.classList.add('hidden');
        });
        contentTypeVideoRadio.addEventListener('change', () => {
            videoContentInput.classList.remove('hidden');
            documentContentInput.classList.add('hidden');
        });

        contentTypeDocumentRadio.addEventListener('change', () => {
            documentContentInput.classList.remove('hidden');
            videoContentInput.classList.add('hidden');
        });

        modifyContentTypeVideoRadio.addEventListener('change', () => {
            modifyVideoContentInput.classList.remove('hidden');
            modifyDocumentContentInput.classList.add('hidden');
        });

        modifyContentTypeDocumentRadio.addEventListener('change', () => {
            modifyDocumentContentInput.classList.remove('hidden');
            modifyVideoContentInput.classList.add('hidden');
        });

        consultInscriptionsButtons.forEach(button => {
            button.addEventListener('click', () => {
                const courseId = button.dataset.courseId;
                const courseTitleElement = button.closest('tr').querySelector('.font-medium.text-gray-900');
                courseTitleInscriptions.textContent = courseTitleElement.textContent;
                // you would fetch inscriptions for courseId here
                // and populate the inscriptionsTableBody.
                inscriptionsTableBody.innerHTML = `
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">Student ${courseId}-1</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">student${courseId}-1@example.com</div>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">Student ${courseId}-2</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">student${courseId}-2@example.com</div>
                        </td>
                    </tr>
                `;
                consultInscriptionsModal.classList.remove('hidden');
            });
        });

        closeInscriptionsModalButton.addEventListener('click', () => {
            consultInscriptionsModal.classList.add('hidden');
        });

        modifyCourseButtons.forEach(button => {
            button.addEventListener('click', () => {
                const courseId = button.dataset.courseId;
                // you would fetch the course details for courseId here
                // and populate the modifyCourseForm fields.
                document.getElementById('modifyCourseId').value = courseId;
                document.getElementById('modifyCourseTitle').value = `Course Title ${courseId}`;
                document.getElementById('modifyCourseDescription').value = `Description for course ${courseId}`;
                document.getElementById('modifyCourseCategory').value = 'Test Category';
                document.getElementById('modifyCourseTags').value = 'tag1, tag2';
                modifyCourseModal.classList.remove('hidden');
            });
        });

        cancelModifyCourseButton.addEventListener('click', () => {
            modifyCourseModal.classList.add('hidden');
        });

        deleteCourseButtons.forEach(button => {
            button.addEventListener('click', () => {
                const courseId = button.dataset.courseId;
                // You might want to store the courseId in the modal for the final deletion action
                deleteConfirmationModal.classList.remove('hidden');
            });
        });

        cancelDeleteCourseButton.addEventListener('click', () => {
            deleteConfirmationModal.classList.add('hidden');
        });
    </script>
</body>
</html>