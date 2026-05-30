<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    /**
     * Display a listing of courses.
     */
    public function index()
    {
        return view('course');
    }

    /**
     * AJAX endpoint: returns the courses table partial.
     */
    public function list()
    {
        $courses = Course::orderBy('course_name')->paginate(10);
        return view('course_list', compact('courses'));
    }

    /**
     * Show form to create a new course.
     */
    public function create()
    {
        return view('addcourse');
    }

    /**
     * Store a newly created course.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_name' => ['required', 'string', 'min:2', 'max:255', 'unique:courses,course_name'],
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            return redirect()->back()->withErrors($validator)->withInput();
        }

        $course = Course::create([
            'course_name' => $request->input('course_name'),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Course created successfully!', 'course' => $course], 201);
        }

        return redirect()->route('course.index')->with('message', 'Course created successfully!');
    }

    /**
     * Show the form for editing the specified course.
     */
    public function edit(string $id)
    {
        $course = Course::findOrFail($id);
        return view('editcourse', compact('course'));
    }

    /**
     * Update the specified course.
     */
    public function update(Request $request, string $id)
    {
        $course = Course::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'course_name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                Rule::unique('courses', 'course_name')->ignore($course->id),
            ],
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            return redirect()->back()->withErrors($validator)->withInput();
        }

        $course->course_name = $request->input('course_name');
        $course->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Course updated successfully!', 'course' => $course]);
        }

        return redirect()->route('course.index')->with('message', 'Course updated successfully!');
    }

    /**
     * Remove the specified course.
     */
    public function destroy(Request $request, string $id)
    {
        $course = Course::findOrFail($id);
        $course->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Course deleted successfully!']);
        }

        return redirect()->route('course.index')->with('message', 'Course deleted successfully!');
    }

    /**
     * Record a student's attendance for a course and assign teacher.
     * - If logged role is teacher: assigns current teacher automatically.
     * - If logged role is admin: requires teacher_id.
     */
    public function enroll(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => ['required', 'exists:courses,id'],
            'student_id' => ['required', 'exists:students,id'],
            'teacher_id' => ['nullable', 'exists:teachers,id'],
            'section' => ['nullable', 'string', 'max:50'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $role = Session::get('logged_role');
        $loggedId = Session::get('logged_id');

        if (!$role || !$loggedId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $teacherId = null;
        if ($role === 'teacher') {
            $teacher = Teacher::where('user_account_id', $loggedId)->first();
            if (!$teacher) {
                return response()->json(['message' => 'Teacher profile not found.'], 422);
            }
            $teacherId = $teacher->id;
        } elseif ($role === 'admin') {
            $teacherId = $request->input('teacher_id');
            if (!$teacherId) {
                return response()->json(['message' => 'Please select a teacher.'], 422);
            }
        } else {
            return response()->json(['message' => 'Access denied.'], 403);
        }

        $courseId = (int) $request->input('course_id');
        $studentId = (int) $request->input('student_id');

        $course = Course::findOrFail($courseId);
        $student = Student::findOrFail($studentId);

        $section = $request->input('section');

        Attendance::updateOrCreate(
            [
                'course_id' => $courseId,
                'student_id' => $studentId,
            ],
            [
                'teacher_id' => $teacherId,
                'section' => $section,
            ]
        );

        return response()->json([
            'message' => 'Attendance recorded successfully!',
            'course' => ['id' => $course->id, 'course_name' => $course->course_name],
            'student' => ['id' => $student->id],
            'teacher_id' => $teacherId,
        ]);
    }

    /**
     * Admin UI page for bulk attendance records.
     */
    public function enrollStudentIndex()
    {
        $role = Session::get('logged_role');
        if ($role !== 'admin') {
            return redirect('/student')->with('msg', 'Access denied.');
        }

        $courses = Course::orderBy('course_name')->get();
        $teachers = Teacher::orderBy('first_name')->orderBy('last_name')->get();

        return view('attendance', compact('courses', 'teachers'));
    }

    /**
     * Returns the students list partial for the bulk attendance page.
     */
    public function enrollStudentStudents(Request $request)
    {
        $role = Session::get('logged_role');
        if ($role !== 'admin') {
            abort(403);
        }

        $q = trim((string) $request->query('q', ''));

        $studentsQuery = Student::query()->with(['degree', 'userAccount']);
        if ($q !== '') {
            $studentsQuery->where(function ($w) use ($q) {
                $w->where('first_name', 'like', "%{$q}%")
                    ->orWhere('middle_name', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%")
                    ->orWhere('contact_no', 'like', "%{$q}%");
            });
        }

        $students = $studentsQuery->orderBy('last_name')->orderBy('first_name')->paginate(10);
        return view('attendance_students', compact('students'));
    }

    /**
     * Bulk record attendance for many students in one course with one teacher.
     */
    public function bulkEnroll(Request $request)
    {
        $role = Session::get('logged_role');
        if ($role !== 'admin') {
            return response()->json(['message' => 'Access denied.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'course_id' => ['required', 'exists:courses,id'],
            'teacher_id' => ['required', 'exists:teachers,id'],
            'section' => ['nullable', 'string', 'max:50'],
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['integer', 'exists:students,id'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $courseId = (int) $request->input('course_id');
        $teacherId = (int) $request->input('teacher_id');
        $section = $request->input('section');
        $studentIds = $request->input('student_ids', []);

        $now = now();
        $rows = [];
        foreach ($studentIds as $sid) {
            $rows[] = [
                'course_id' => $courseId,
                'student_id' => (int) $sid,
                'teacher_id' => $teacherId,
                'section' => $section,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('attendances')->upsert(
            $rows,
            ['course_id', 'student_id'],
            ['teacher_id', 'section', 'updated_at']
        );

        return response()->json([
            'message' => 'Attendance recorded successfully!',
            'count' => count($rows),
        ]);
    }
}

