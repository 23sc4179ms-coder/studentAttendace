<?php
// One-off script to create admin user and sample Course/Student/Attendance
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\UserAccount;
use App\Models\Course;
use App\Models\Student;
use App\Models\Attendance;

// Create or update admin user
$admin = UserAccount::updateOrCreate(
    ['username' => 'admin'],
    [
        'email' => 'admin@example.com',
        'password' => password_hash('AdminPass123!', PASSWORD_BCRYPT),
        'role' => 'admin',
        'is_active' => 1,
        'must_change_password' => 0
    ]
);

echo "Admin user id: {$admin->id}\n";

// Create sample course
$course = Course::firstOrCreate(['course_name' => 'Sample Course']);
echo "Course id: {$course->id}\n";

// Create sample student
$student = Student::create([
    'first_name' => 'Test',
    'middle_name' => '',
    'last_name' => 'Student',
    'email' => 'student@example.com',
    'contact_no' => '09171234567'
]);

echo "Student id: {$student->id}\n";

// Create attendance linking student to course
$attendance = Attendance::create([
    'course_id' => $course->id,
    'student_id' => $student->id,
    'teacher_id' => null,
    'section' => 'A'
]);

echo "Attendance id: {$attendance->id}\n";

