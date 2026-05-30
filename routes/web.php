<?php

use App\Http\Controllers\StudentController;
use App\Http\Controllers\DegreeController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ExportController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return 'pong';
});

Route::get('/health', function () {
    return response('ok', 200);
});
Route::get('/', [UserController::class, 'login']);
Route::post('/', [UserController::class, 'login']);
Route::get('/home', [StudentController::class, 'studentHome'])->name('homeRoute');
Route::get('/about', [StudentController::class, 'studentAbout']);
Route::resource('/changePassword', UserController::class);
Route::get('/logout', [UserController::class, 'logout'])->name('logout');

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