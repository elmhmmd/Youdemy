<?php
session_start();

require_once '../classes/database.php';
require_once '../classes/course.php';
require_once '../classes/category.php';
require_once '../classes/enrollments.php';
require_once '../classes/user.php';
require_once '../classes/teacher.php';

// Redirect non-students
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header('Location: login.php');
    exit();
}

$studentId = $_SESSION['user_id'];
$enrollment = new enrollments();
$courseObj = new course();
$categoryObj = new category();
$teacherObj = new teacher();

// Get enrolled courses
$enrolledCourses = $enrollment->getEnrolledCoursesByStudentId($studentId);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Cours - Youdemy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .truncate-3-lines {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
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
                <div class="flex items-center space-x-4">
                    <a href="catalogue.php" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                        Catalogue
                    </a>
                    <a href="../actions/logout.php" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                        Déconnexion
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h2 class="text-2xl font-semibold text-gray-900 mb-6">Mes Cours</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if (!empty($enrolledCourses)): ?>
        <?php foreach ($enrolledCourses as $course): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                <div class="p-6">
                    <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mb-2">
                        <?= htmlspecialchars($course['category_name']) ?>
                    </span>
                    
                    <h3 class="text-xl font-semibold mb-2">
                        <?= htmlspecialchars($course['title']) ?>
                    </h3>

                    <p class="text-sm text-gray-500 mb-2">
                        Par <?= htmlspecialchars($course['teacher_name']) ?>
                    </p>

                    <div class="mt-4">
                        <a href="course_content.php?id=<?= $course['course_id'] ?>" 
                           class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                            Accéder au cours
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-span-full text-center py-12">
            <p class="text-gray-500 mb-4">Aucun cours trouvé dans vos inscriptions.</p>
            <a href="catalogue.php" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                Parcourir les cours
            </a>
        </div>
    <?php endif; ?>
</div>
    </main>
</body>
</html>