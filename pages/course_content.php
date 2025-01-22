<?php
session_start();

require_once '../classes/database.php';
require_once '../classes/course.php';
require_once '../classes/enrollments.php';
require_once '../classes/user.php';
require_once '../classes/teacher.php';
require_once '../classes/category.php';

// Check if user is logged in as student
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header('Location: login.php');
    exit();
}

// Check if course ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: mes_cours.php');
    exit();
}

$courseId = (int)$_GET['id'];
$studentId = (int)$_SESSION['user_id'];

// Verify enrollment
$enrollments = new enrollments();
if (!$enrollments->isEnrolled($studentId, $courseId)) {
    header('Location: mes_cours.php');
    exit();
}

// Get course details
$courseObj = new course();
$course = $courseObj->getCourseById($courseId);
$teacherObj = new teacher();
$categoryObj = new category();

if (!$course) {
    header('Location: mes_cours.php');
    exit();
}

// Get additional course info
$teacherName = $teacherObj->getTeacherNameById($course->getTeacherId());
$categoryName = $categoryObj->getCategoryNameById($course->getCategoryId());

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($course->getTitle()) ?> - Youdemy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 flex items-center">
                    <a href="mes_cours.php" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-arrow-left mr-2"></i>
                    </a>
                    <span class="text-lg font-semibold"><?= htmlspecialchars($course->getTitle()) ?></span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="mes_cours.php" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                        Mes Cours
                    </a>
                    <a href="../actions/logout.php" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                        Déconnexion
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Course Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <!-- Course Metadata -->
            <div class="mb-6">
                <div class="flex items-center space-x-4 mb-4">
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                        <?= htmlspecialchars($categoryName) ?>
                    </span>
                    <span class="text-gray-500 text-sm">
                        Par <?= htmlspecialchars($teacherName) ?>
                    </span>
                </div>
                <h1 class="text-2xl font-bold mb-4"><?= htmlspecialchars($course->getTitle()) ?></h1>
                <p class="text-gray-600 mb-6"><?= htmlspecialchars($course->getDescription()) ?></p>
            </div>

            <!-- Content Display -->
            <div class="border-t pt-6">
                <?php if ($course->getContentType() === 'video'): ?>
                    <!-- Video Player -->
                    <div class="aspect-w-16 aspect-h-9 bg-black rounded-lg overflow-hidden">
                        <iframe 
                            src="<?= htmlspecialchars($course->getContentUrl()) ?>" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen
                            class="w-full h-full"
                        ></iframe>
                    </div>
                <?php elseif ($course->getContentType() === 'document'): ?>
                    <!-- Document Viewer -->
                    <div class="bg-gray-50 p-6 rounded-lg font-mono whitespace-pre-wrap">
                        <?= nl2br(htmlspecialchars($course->getContentText())) ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12 text-gray-500">
                        Contenu non disponible
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>
