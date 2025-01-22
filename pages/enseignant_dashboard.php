<?php
session_start();

require_once '../classes/database.php';
require_once '../classes/user.php';
require_once '../classes/course.php';
require_once '../classes/category.php';
require_once '../classes/tag.php';
require_once '../classes/teacher.php';
require_once '../classes/enrollments.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

if ($_SESSION['role_id'] != 2) {
    header('Location: ../pages/catalogue.php');
    exit();
}

$teacherId = $_SESSION['user_id'];
$courseObj = new course();
$categoryObj = new category();
$teacherObj = new teacher();
$enrollmentObj = new enrollments();

// Get teacher-specific data
$courses = $courseObj->getCoursesByTeacherId($teacherId);
$categories = $categoryObj->getAllCategories();
$tags = (new Tag())->getAllTags();

// Statistics
$courseCount = count($courses);
$totalStudents = $enrollmentObj->getTotalStudentsForTeacher($teacherId);
$totalVideos = $courseObj->getTotalVideosForTeacher($teacherId);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Enseignant - Youdemy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                    <button id="addCourseButton" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">
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
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white shadow rounded-md p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-500 text-white rounded-md p-2">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="ml-4">
                        <dt class="text-sm font-medium text-gray-500">Étudiants Inscrits</dt>
                        <dd class="text-lg font-semibold text-gray-900"><?= $totalStudents ?></dd>
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
                        <dd class="text-lg font-semibold text-gray-900"><?= $totalVideos ?></dd>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Table -->
        <div class="bg-white shadow rounded-md overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Titre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catégorie</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Inscrits</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($courses as $course): ?>
                        <tr data-course-id="<?= $course->getCourseId() ?>">
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($course->getTitle()) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                                    <?= htmlspecialchars($categoryObj->getCategoryNameById($course->getCategoryId())) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?= $enrollmentObj->getEnrollmentCount($course->getCourseId()) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                                <button class="consult-course text-blue-600 hover:text-blue-900" 
                                        data-course-id="<?= $course->getCourseId() ?>">
                                    Consulter
                                </button>
                                <button class="modify-course text-green-600 hover:text-green-900"
                                        data-course-id="<?= $course->getCourseId() ?>">
                                    Modifier
                                </button>
                                <button class="delete-course text-red-600 hover:text-red-900"
                                        data-course-id="<?= $course->getCourseId() ?>">
                                    Supprimer
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Add Course Modal -->
        <div id="addCourseModal" class="hidden fixed z-10 inset-0 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <form action="../actions/add_course.php" method="POST" enctype="multipart/form-data">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Ajouter un nouveau cours</h3>
                                    
                                    <div class="mb-4">
                                        <label for="courseTitle" class="block text-gray-700 text-sm font-bold mb-2">Titre</label>
                                        <input type="text" id="courseTitle" name="title" required
                                            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>

                                    <div class="mb-4">
                                        <label for="courseDescription" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                                        <textarea id="courseDescription" name="description" required
                                            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            rows="4"></textarea>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Type de contenu</label>
                                        <div class="flex items-center space-x-4">
                                            <label class="flex items-center">
                                                <input type="radio" name="content_type" value="video" checked 
                                                    class="form-radio h-4 w-4 text-blue-600 transition duration-150 ease-in-out">
                                                <span class="ml-2">Vidéo</span>
                                            </label>
                                            <label class="flex items-center">
                                                <input type="radio" name="content_type" value="document"
                                                    class="form-radio h-4 w-4 text-blue-600 transition duration-150 ease-in-out">
                                                <span class="ml-2">Document</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="mb-4" id="videoField">
                                        <label for="contentUrl" class="block text-gray-700 text-sm font-bold mb-2">URL de la vidéo</label>
                                        <input type="url" id="contentUrl" name="content_url"
                                            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>

                                    <div class="mb-4 hidden" id="documentField">
                                        <label for="contentDocument" class="block text-gray-700 text-sm font-bold mb-2">Téléverser un document</label>
                                        <input type="file" id="contentDocument" name="content_document"
                                            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            accept=".pdf,.doc,.docx,.txt">
                                    </div>

                                    <div class="mb-4">
                                        <label for="courseCategory" class="block text-gray-700 text-sm font-bold mb-2">Catégorie</label>
                                        <select id="courseCategory" name="category_id" required
                                            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?= $category->getCategoryId() ?>">
                                                    <?= htmlspecialchars($category->getCategoryName()) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="mb-4">
                                        <label for="courseTags" class="block text-gray-700 text-sm font-bold mb-2">Tags</label>
                                        <select id="courseTags" name="tags[]" multiple
                                            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            style="height: 100px;">
                                            <?php foreach ($tags as $tag): ?>
                                                <option value="<?= $tag['tag_id'] ?>">
                                                    <?= htmlspecialchars($tag['tag_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <p class="text-sm text-gray-500 mt-1">Maintenez Ctrl/Cmd pour sélectionner multiple tags</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" 
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto">
                                Enregistrer
                            </button>
                            <button type="button" 
                                    onclick="closeAddModal()"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Consultation Modal -->
        <div id="consultModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 overflow-y-auto">
            <div class="relative mx-auto p-4 w-full max-w-2xl top-20">
                <div class="bg-white rounded-lg shadow-xl">
                    <div class="flex justify-between items-start p-4 border-b">
                        <h3 class="text-xl font-semibold" id="consultTitle"></h3>
                        <button onclick="closeConsultModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="p-6 space-y-4">
                        <div class="mb-4">
                            <label class="block text-gray-700 font-bold mb-2">Description:</label>
                            <p id="consultDescription" class="text-gray-600 whitespace-pre-wrap"></p>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 font-bold mb-2">Étudiants inscrits:</label>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <table class="w-full">
                                    <thead>
                                        <tr>
                                            <th class="text-left py-2">Nom</th>
                                            <th class="text-left py-2">Email</th>
                                        </tr>
                                    </thead>
                                    <tbody id="consultStudents">
                                        <!-- Les étudiants seront ajoutés dynamiquement ici -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button onclick="closeConsultModal()" 
                                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                                Fermer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Gestion des modales
        const addCourseButton = document.getElementById('addCourseButton');
        const addCourseModal = document.getElementById('addCourseModal');
        const videoRadio = document.querySelector('input[value="video"]');
        const documentRadio = document.querySelector('input[value="document"]');
        const videoField = document.getElementById('videoField');
        const documentField = document.getElementById('documentField');

        // Ouvrir modal d'ajout
        addCourseButton.addEventListener('click', () => {
            addCourseModal.classList.remove('hidden');
        });

        // Fermer modal d'ajout
        function closeAddModal() {
            addCourseModal.classList.add('hidden');
        }

        // Basculer entre les types de contenu
        function toggleContentFields() {
            if (videoRadio.checked) {
                videoField.classList.remove('hidden');
                documentField.classList.add('hidden');
                documentField.querySelector('input').removeAttribute('required');
                videoField.querySelector('input').setAttribute('required', '');
            } else {
                videoField.classList.add('hidden');
                documentField.classList.remove('hidden');
                videoField.querySelector('input').removeAttribute('required');
                documentField.querySelector('input').setAttribute('required', '');
            }
        }

        videoRadio.addEventListener('change', toggleContentFields);
        documentRadio.addEventListener('change', toggleContentFields);
        toggleContentFields();

        // Gestion de la consultation
        document.querySelectorAll('.consult-course').forEach(button => {
            button.addEventListener('click', async () => {
                const courseId = button.dataset.courseId;
                try {
                    const response = await fetch(`../actions/get_course_details.php?course_id=${courseId}`);
                    const data = await response.json();
                    
                    document.getElementById('consultTitle').textContent = data.title;
                    document.getElementById('consultDescription').textContent = data.description;
                    
                    const tbody = document.getElementById('consultStudents');
                    tbody.innerHTML = data.students.map(student => `
                        <tr>
                            <td class="py-2">${student.username}</td>
                            <td class="py-2">${student.email}</td>
                        </tr>
                    `).join('');
                    
                    document.getElementById('consultModal').classList.remove('hidden');
                } catch (error) {
                    console.error('Error:', error);
                    alert('Erreur lors du chargement des détails du cours');
                }
            });
        });

        // Fermer modal de consultation
        function closeConsultModal() {
            document.getElementById('consultModal').classList.add('hidden');
        }

        // Suppression de cours
        document.querySelectorAll('.delete-course').forEach(button => {
            button.addEventListener('click', async () => {
                const courseId = button.dataset.courseId;
                if (confirm('Êtes-vous sûr de vouloir supprimer ce cours ?')) {
                    try {
                        const formData = new FormData();
                        formData.append('course_id', courseId);
                        
                        const response = await fetch('../actions/delete_course.php', {
                            method: 'POST',
                            body: formData
                        });
                        
                        if (response.ok) {
                            location.reload();
                        } else {
                            alert('Erreur lors de la suppression');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Erreur lors de la suppression');
                    }
                }
            });
        });
    </script>
</body>
</html>