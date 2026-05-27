<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\CalculateController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PSUController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\DegreeController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ExportController;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

// ========== TEST ROUTE (must work even if middleware breaks) ==========
Route::get('/ping', function () {
    return 'pong';
});

Route::get('/health', function () {
    return response('ok', 200);
});

// Dev: create admin and sample data (temporary)
Route::get('/dev/create-admin', [\App\Http\Controllers\DevController::class, 'createAdmin']);

// Dev: insert Emmanuel Garcia with ELECTIVE 1 & ELECTIVE 2 (idempotent)
Route::get('/dev/insert-emmanuel-electives', function () {
    $studentFirstName = 'Emmanuel';
    $studentLastName = 'Garcia';
    $studentEmail = 'emmanuel.garcia@example.test';

    $courseNames = ['ELECTIVE 1', 'ELECTIVE 2'];

    $result = DB::transaction(function () use ($studentFirstName, $studentLastName, $studentEmail, $courseNames) {
        $student = \App\Models\Student::firstOrCreate(
            ['email' => $studentEmail],
            [
                'first_name' => $studentFirstName,
                'middle_name' => null,
                'last_name' => $studentLastName,
                'contact_no' => null,
                'degree_id' => null,
                'user_account_id' => null,
            ]
        );

        if ($student->first_name !== $studentFirstName || $student->last_name !== $studentLastName) {
            $student->first_name = $studentFirstName;
            $student->last_name = $studentLastName;
            $student->save();
        }

        $courseIds = [];
        foreach ($courseNames as $courseName) {
            $course = \App\Models\Course::firstOrCreate(['course_name' => $courseName]);
            $courseIds[$course->id] = ['teacher_id' => null];
        }

        // Prevent duplicates in the pivot table.
        $student->courses()->syncWithoutDetaching($courseIds);

        return [
            'full_name' => trim($studentFirstName . ' ' . $studentLastName),
            'course_names' => $courseNames,
        ];
    });

    $lines = [
        $result['full_name'] . ' - ' . $result['course_names'][0],
        $result['full_name'] . ' - ' . $result['course_names'][1],
    ];

    return response(implode("\n", $lines), 200)
        ->header('Content-Type', 'text/plain; charset=UTF-8');
});

// ========== ROOT LOGIN ROUTES ==========
Route::get('/', [UserController::class, 'login']);
Route::post('/', [UserController::class, 'login']);

// ========== OTHER PUBLIC ROUTES ==========
Route::get('/home', [StudentController::class, 'studentHome'])->name('homeRoute');
Route::get('/about', [StudentController::class, 'studentAbout']);
Route::get('/user_profile', [PagesController::class, 'userProfile']);
Route::get('/user_posts', [PagesController::class, 'userPosts']);
Route::get('/student_courses', [PagesController::class, 'studentCourses']);
Route::get('/demo', [PagesController::class, 'demo']);
Route::resource('/changePassword', UserController::class);
Route::get('/maintenance', [PagesController::class, 'maintenance'])->name('maintenance');
Route::get('/logout', [UserController::class, 'logout'])->name('logout');

// ========== ACTIVE APP ROUTES ==========
// These routes are registered without the broken middleware group so named routes
// used by redirects and views are always available on Render.
Route::get('/profile', [ClientController::class, 'displayProfile'])->name('profile');
Route::get('/dashboard', [ClientController::class, 'displayDashboard'])->name('greet');
Route::get('/aboutus', [ClientController::class, 'displayAboutUs'])->name('aboutus');

Route::get('/student/list', [StudentController::class, 'list'])->name('student.list');
Route::get('/teacher/list', [TeacherController::class, 'list'])->name('teacher.list');
Route::get('/course/list', [CourseController::class, 'list'])->name('course.list');

Route::post('/course/attendance', [CourseController::class, 'enroll'])->name('course.attendance');
Route::post('/course/bulk-attendance', [CourseController::class, 'bulkEnroll'])->name('course.bulkAttendance');

Route::get('/attendance', [CourseController::class, 'enrollStudentIndex'])->name('attendance.index');
Route::get('/attendance/students', [CourseController::class, 'enrollStudentStudents'])->name('attendance.students');

Route::get('/student/{id}/json', [StudentController::class, 'showJson'])->name('student.showJson');
Route::get('/teacher/{id}/json', [TeacherController::class, 'showJson'])->name('teacher.showJson');

Route::get('/export/students-teachers', [ExportController::class, 'studentsTeachers'])->name('export.studentsTeachers');

Route::resource('/student', StudentController::class);
Route::resource('/teacher', TeacherController::class);

Route::get('/studentDashboard/course/{course}/details', [UserController::class, 'studentCourseDetails'])
    ->name('studentDashboard.course.details');
Route::resource('/studentDashboard', UserController::class);

Route::get('/manageStudents', [StudentController::class, 'manageStudents'])->name('manageStudents');
Route::get('/teacherDashboard', [UserController::class, 'teacherDashboard'])->name('teacherDashboard.index');
Route::get('/teacherDashboard/{id}/edit', [UserController::class, 'edit'])->name('teacherDashboard.edit');
Route::put('/teacherDashboard/{id}', [UserController::class, 'update'])->name('teacherDashboard.update');
Route::get('/teacherDashboard/course/{course}/attendance', [UserController::class, 'enrolledStudents'])
    ->name('teacherDashboard.course.attendance');

Route::resource('/degree', DegreeController::class);
Route::resource('/course', CourseController::class)->except(['show']);

// ========== MIDDLEWARE GROUP (temporarily commented out to avoid 500 from broken middleware) ==========
// Uncomment after fixing your middleware classes (SessionUserAccountMW, DownForMaintenanceMW, etc.)
/*
Route::middleware(['group_middleware','sessionUserAccount','maintenance'])->group(function(){
        
    Route::get('/profile', [ClientController::class,'displayProfile'])->name('profile');
    Route::get('/dashboard', [ClientController::class,'displayDashboard'])->name('greet');
    Route::get('/aboutus', [ClientController::class,'displayAboutUs'])->name('aboutus');

    // AJAX list endpoints
    Route::get('/student/list', [StudentController::class, 'list'])->name('student.list');
    Route::get('/teacher/list', [TeacherController::class, 'list'])->name('teacher.list');
    Route::get('/course/list', [CourseController::class, 'list'])->name('course.list');

    // Attendance
    Route::post('/course/attendance', [CourseController::class, 'enroll'])->name('course.attendance');
    Route::post('/course/bulk-attendance', [CourseController::class, 'bulkEnroll'])->name('course.bulkAttendance');

    // Admin UI
    Route::get('/attendance', [CourseController::class, 'enrollStudentIndex'])->name('attendance.index');
    Route::get('/attendance/students', [CourseController::class, 'enrollStudentStudents'])->name('attendance.students');

    // AJAX view endpoints
    Route::get('/student/{id}/json', [StudentController::class, 'showJson'])->name('student.showJson');
    Route::get('/teacher/{id}/json', [TeacherController::class, 'showJson'])->name('teacher.showJson');

    // Export Excel
    Route::get('/export/students-teachers', [ExportController::class, 'studentsTeachers'])->name('export.studentsTeachers');

    Route::resource('/student', StudentController::class);
    Route::resource('/teacher', TeacherController::class);
    
    // Student dashboard AJAX
    Route::get('/studentDashboard/course/{course}/details', [UserController::class, 'studentCourseDetails'])->name('studentDashboard.course.details');

    Route::resource('/studentDashboard', UserController::class);
    Route::get('/manageStudents', [StudentController::class, 'manageStudents'])->name('manageStudents');
    Route::get('/teacherDashboard', [UserController::class, 'teacherDashboard'])->name('teacherDashboard.index');
    Route::get('/teacherDashboard/{id}/edit', [UserController::class, 'edit'])->name('teacherDashboard.edit');
    Route::put('/teacherDashboard/{id}', [UserController::class, 'update'])->name('teacherDashboard.update');
    Route::get('/teacherDashboard/course/{course}/attendance', [UserController::class, 'enrolledStudents'])->name('teacherDashboard.course.attendance');
    Route::resource('/degree', DegreeController::class);
    Route::resource('/course', CourseController::class)->except(['show']);
});
*/

// Route::get('/', function () {
//    return view('portal_welcome');
// });

// Route::get('/employees', function () {
//    return view('portal_welcome');
// });



// Route::get('/greetings', [ClientController::class,'displayGreetings'])->name('greet');
// Route::resource('/client', ClientController::class);

// Route::get('/profile', [ClientController::class,'displayProfile'])->name('greet');
// Route::get('/dashboard', [ClientController::class,'displayDashboard'])->name('greet');
// Route::get('/aboutus', [ClientController::class,'displayAboutUs'])->name('greet');



// Route::get('/', function () {
//    return view('portal_welcome');
    
// })->name('mainpage');


// Route::get('/about', function () {
//     $a = 3;
//     $b = 4;
//     $sum = $a + $b;
//     return  $sum;
    
// });
// Route::get("/add ", [CalculateController::class,'add']);
// Route::get("/min ", [CalculateController::class,'minus']);
// Route::get("/quo ", [CalculateController::class,'quotient']);
// Route::get("/prod ", [CalculateController::class,'product']);
// Route::get("/mod ", [CalculateController::class,'mod']);

// Route::resource('/clients', ClientController::class);

// // Simple auth resource routes (login/register)
// Route::get('/', function () {
//     return view('portal_welcome');
// })->name('home');


// Route::get('/welcome1', [PSUController::class, 'welcome'])->name('welcome');
// Route::get('/mission', [PSUController::class, 'mission'])->name('mission');
// Route::get('/vision', [PSUController::class, 'vision'])->name('vision');
// Route::get('/eoms-policy', [PSUController::class, 'EOMSPolicy'])->name('EOMSPolicy');
// Route::get('/dynamic', [PSUController::class, 'dynamic'])->name('dynamic');

// Route::get('/student/{name}/{course}', [PSUController::class, 'student'])->name('student');

// Route::resource('/students', StudentController::class);

// Route::resource('auth', AuthController::class)->only(['index','create','store']);
// Route::post('auth/login', [AuthController::class, 'login'])->name('auth.login');
// Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');


#Route::get('/about', function () {
 #   return view('about');
    
#})->name('aboutpage');



#Route::get('/shop/{shopID}', function ($shopID) {
 #   return view('shop', ['shopID' => $shopID]);
#});

#Route::get('/employee/{employeeID}', function ($employeeID) {
 #   return view('employee', ['employeeID' => $employeeID]);
#});

#Route::get('/student/{studentID?}', function ($studentID = null) {
 #   return view('student', ['studentID' => $studentID]);
#});

#Route::get('/sales/{salesID?}', function ($salesID = null) {
 #   return view('sales', ['salesID' => $salesID]);
#});

#Route::get("/greets/{fname?}/msg/{msg?}",function ($fname = "user",$msg="ok po"){
 #   return "Welcome ".$fname." message: ".$msg."!";
#} );

#Route::get("/search/{id}",function ($id){
 #   return "ID: ".$id;
#} )->name("searchRoute")->where("id","[0-9]+");



#Route::prefix("admin")->group(
 #   function () {
  #      Route::get("/dashboard",function (){
   #     return "this is admin dashboard";
    #    });
     #   Route::get("/profile",function (){
      #  return "this is admin profile";
       # });
       # Route::get("/configuration",function (){
       # return "this is admin configuration";
       # });

    #}
#);
    #task 1:Creating Named Routes
// Route::get("/home",function (){
//     return "I am Mark Lhemuel Arenas. Welcome to the Homepage! ";
// } )->name("home.page");

//     #task 2:
// Route::get("/redirect",function (){
//     return redirect()->route("home.page");
// } )->name("redirect");
//     #task 3
// Route::get("/greet/{name}",function ($name){
//     return "Hello ".$name;
// } )->name("required");
//     #task 4
// Route::get("/student/{name?}",function ($name="Student"){
//     return "Hello ".$name;
// } )->name("optional");
//     #task 5
// Route::prefix("administrator")->group(function() {
//     Route::get("/Dashboard",function(){
//     return "Dashboard";
//     })->name("dashboard");

//     Route::get("/Profile",function(){
//     return "Welcome to my Page";
//     });    

//     Route::get("/Settings",function(){
//     return "Setting Page";
//     });
// }
// )->name("group");
//     #task 6
// Route::get("/dashboardredirect",function (){
//     return redirect()->route("dashboard");
// } )->name("redirect");