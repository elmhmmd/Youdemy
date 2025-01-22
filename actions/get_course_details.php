<?php
require_once '../classes/database.php';
require_once '../classes/course.php';
require_once '../classes/enrollments.php';

header('Content-Type: application/json');

$courseId = $_GET['course_id'] ?? null;

try {
    $courseObj = new course();
    $course = $courseObj->getCourseById($courseId);
    
    $enrollmentObj = new enrollments();
    $students = $enrollmentObj->getCourseEnrollments($courseId);

    echo json_encode([
        'title' => $course->getTitle(),
        'description' => $course->getDescription(),
        'students' => $students
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}