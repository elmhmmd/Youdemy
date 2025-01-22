<?php
require_once '../classes/course.php';
require_once '../classes/category.php';
require_once '../classes/database.php';
require_once '../classes/user.php';
require_once '../classes/teacher.php';

if (isset($_SESSION['user']) && $_SESSION['user']['role_id'] == 2) {
    header('Location: ./enseignant_dashboard.php');
    exit();
}

if (isset($_SESSION['user']) && $_SESSION['user']['role_id'] == 1) {
    header('Location: ./admin_dashboard.php');
    exit();
}

$perPage = 6;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$currentPage = max(1, $currentPage);

$courseObj = new course();
$allCourses = $searchTerm ? $courseObj->searchCourses($searchTerm) : $courseObj->getAllCourses();

$categoryObj = new category();
$teacherObj = new teacher();

$totalCourses = count($allCourses);
$totalPages = ceil($totalCourses / $perPage);

$startIndex = ($currentPage - 1) * $perPage;
$courses = array_slice($allCourses, $startIndex, $perPage);

if (isset($_POST['inscribe']) && isset($_POST['course_id'])) {
    if (!isset($_SESSION['user'])) {
        header('Location: ./login.php');
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogue des Cours - Youdemy</title>
    <style>
            .truncate-3-lines {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gradient-to-b from-blue-50 to-white min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 flex items-center">
                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                    </svg>
                    <span class="ml-2 text-2xl font-bold text-gray-900">Youdemy</span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Search Bar -->
        <div class="mb-8 relative">
            <input type="text" placeholder="Rechercher des cours..." 
                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button class="absolute right-3 top-3 text-gray-400">
                <i class="fas fa-search"></i>
            </button>
        </div>

        
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <?php foreach ($courses as $course): ?>
    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
    <div class="p-6">
        <!-- Category Badge -->
        <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mb-2">
            <?= htmlspecialchars($categoryObj->getCategoryNameById($course->getCategoryId())) ?>
        </span>
        
        <!-- Course Title -->
        <h3 class="text-xl font-semibold mb-2">
            <?= htmlspecialchars($course->getTitle()) ?>
        </h3>

        <!-- Instructor -->
        <p class="text-sm text-gray-500 mb-2">
            Par <?= htmlspecialchars($teacherObj->getTeacherNameById($course->getTeacherId())) ?>
        </p>

        <!-- Description -->
        <p class="text-gray-600 text-sm mb-4 truncate-3-lines">
            <?= htmlspecialchars($course->getDescription()) ?>
        </p>

        <!-- Hidden Data Container -->
        <div class="hidden course-data"
            data-title="<?= htmlspecialchars($course->getTitle()) ?>"
            data-description="<?= htmlspecialchars($course->getDescription()) ?>"
            data-category="<?= htmlspecialchars($categoryObj->getCategoryNameById($course->getCategoryId())) ?>"
            data-instructor="<?= htmlspecialchars($teacherObj->getTeacherNameById($course->getTeacherId())) ?>"
            data-content-type="<?= htmlspecialchars($course->getContentType()) ?>">
        </div>

        <!-- Details Button -->
        <button onclick="showCourseDetails(this)" 
                class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
            Voir les détails
        </button>
    </div>
</div>
    <?php endforeach; ?>
</div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
<div class="flex justify-center gap-2 mt-8">
    <?php for ($page = 1; $page <= $totalPages; $page++): ?>
        <a href="?page=<?= $page ?>" 
           class="px-4 py-2 rounded-md <?= ($page === $currentPage) ? 'bg-blue-600 text-white' : 'bg-white border hover:bg-blue-50' ?>">
            <?= $page ?>
        </a>
    <?php endfor; ?>
</div>
<?php endif; ?>
    </main>

    <!-- Course Details Modal -->
    <div id="courseModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 overflow-y-auto">
    <div class="relative mx-auto p-4 w-full max-w-2xl top-20">
        <div class="bg-white rounded-lg shadow-xl">
            <!-- Modal Header -->
            <div class="flex justify-between items-start p-4 border-b">
                <h3 class="text-xl font-semibold" id="modalTitle"></h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="p-6 space-y-4">
                <p class="text-gray-600" id="modalDescription"></p>
                
                <div class="flex flex-wrap gap-2">
                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-sm" id="modalCategory"></span>
                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded-full text-sm" id="modalContentType"></span>
                </div>

                <div class="space-y-2">
                    <p><strong>Instructeur:</strong> <span id="modalInstructor"></span></p>
                    <!-- Add more fields if needed -->
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end p-4 border-t gap-3">
                <button onclick="handleInscription()" 
                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                    S'inscrire
                </button>
                <button onclick="closeModal()" 
                        class="bg-gray-100 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-200">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>

    <script>
        function showCourseDetails(courseId) {
            // In real implementation, fetch course details based on ID
            document.getElementById('courseModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('courseModal').classList.add('hidden');
        }

        function handleInscription() {
            if(localStorage.getItem('authToken')) {
                // Handle inscription logic
                alert('Inscription réussie !');
                closeModal();
            } else {
                if(confirm('Vous devez être connecté pour vous inscrire. Voulez-vous aller à la page de connexion ?')) {
                    window.location.href = './login.php';
                }
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if(event.target.id === 'courseModal') {
                closeModal();
            }
        }
    </script>
</body>
</html>