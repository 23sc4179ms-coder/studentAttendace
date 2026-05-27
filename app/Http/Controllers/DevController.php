<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserAccount;
use App\Models\Course;
use App\Models\Student;
use App\Models\Attendance;

class DevController extends Controller
{
    public function createAdmin(Request $request)
    {
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

        $course = Course::firstOrCreate(['course_name' => 'Sample Course']);

        $student = Student::create([
            'first_name' => 'Test',
            'middle_name' => '',
            'last_name' => 'Student',
            'email' => 'student@example.com',
            'contact_no' => '09171234567'
        ]);

        $attendance = Attendance::create([
            'course_id' => $course->id,
            'student_id' => $student->id,
            'teacher_id' => null,
            'section' => 'A'
        ]);

        return response()->json([
            'admin_id' => $admin->id,
            'course_id' => $course->id,
            'student_id' => $student->id,
            'attendance_id' => $attendance->id
        ]);
    }
}
