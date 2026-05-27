<?php

namespace App\Http\Controllers;
use App\Models\UserAccount;
use App\Models\Student;
use App\Models\Degree;
use App\Models\Course;
use App\Models\Attendance;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Log;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function changePass(){
        return view('portal_change_password');
    }
    public function index()
    {
        //
        $userId = Session::get('logged_id');
        
        if (!$userId) {
            return redirect('/');
        }

        $user = UserAccount::find($userId);
        if (!$user) {
            return redirect('/');
        }

        $logged_user = Session::get('logged_user');
        $logged_role = Session::get('logged_role');

        if($logged_role === 'student'){
        if ($user->must_change_password) {
            return redirect()->route('studentDashboard.edit', $user->id);
        }

        $student = Student::where('user_account_id', $userId)->first();
        $attendanceCourses = collect();
        if ($student) {
            $attendanceCourses = Course::query()
                ->join('attendances', 'attendances.course_id', '=', 'courses.id')
                ->where('attendances.student_id', $student->id)
                ->select('courses.*', 'attendances.section', 'attendances.teacher_id')
                ->orderBy('courses.course_name')
                ->get();
        }

        return view('portal_student_dashboard', [
            'user' => $user,
            'logged_user' => $logged_user,
            'logged_role' => $logged_role,
            'student' => $student,
            'attendanceCourses' => $attendanceCourses,
        ]);
        }

        if ($logged_role === 'teacher') {
            return redirect()->route('teacherDashboard.index');
        }

        if ($logged_role === 'admin') {
            return redirect()->route('manageStudents');
        }

        return redirect('/');
    }

    public function teacherDashboard()
    {
        $userId = Session::get('logged_id');
        if (!$userId) {
            return redirect('/');
        }

        $user = UserAccount::find($userId);
        if (!$user) {
            return redirect('/');
        }

        $logged_user = Session::get('logged_user');
        $logged_role = Session::get('logged_role');

        if ($logged_role !== 'teacher') {
            if ($logged_role === 'student') {
                return redirect()->route('studentDashboard.index');
            }
            if ($logged_role === 'admin') {
                return redirect()->route('manageStudents');
            }
            return redirect('/');
        }

        $teacher = Teacher::where('user_account_id', $userId)->first();
        $courses = collect();
        if ($teacher) {
            $courses = Course::query()
                ->select('courses.*', DB::raw('COUNT(attendances.id) as attendance_count'))
                ->join('attendances', 'attendances.course_id', '=', 'courses.id')
                ->where('attendances.teacher_id', $teacher->id)
                ->groupBy('courses.id', 'courses.course_name', 'courses.created_at', 'courses.updated_at')
                ->orderBy('courses.course_name')
                ->get();
        }

        return view('portal_teacher_dashboard', [
            'user' => $user,
            'logged_user' => $logged_user,
            'logged_role' => $logged_role,
            'teacher' => $teacher,
            'courses' => $courses,
        ]);
    }

    public function enrolledStudents(Request $request, Course $course)
    {
        $userId = Session::get('logged_id');
        $logged_role = Session::get('logged_role');

        if (!$userId || $logged_role !== 'teacher') {
            abort(403);
        }

        $teacher = Teacher::where('user_account_id', $userId)->first();
        if (!$teacher) {
            abort(403);
        }

        $students = Student::query()
            ->join('attendances', 'attendances.student_id', '=', 'students.id')
            ->where('attendances.course_id', $course->id)
            ->where('attendances.teacher_id', $teacher->id)
            ->select('students.*')
            ->with(['degree', 'userAccount'])
            ->paginate(5);

        return view('portal_attended', [
            'course' => $course,
            'students' => $students,
        ]);
    }

    public function studentCourseDetails(Request $request, Course $course)
    {
        $userId = Session::get('logged_id');
        $role = Session::get('logged_role');
        if (!$userId || $role !== 'student') {
            abort(403);
        }

        $student = Student::where('user_account_id', $userId)->first();
        if (!$student) {
            abort(403);
        }

        $attendance = Attendance::query()
            ->where('course_id', $course->id)
            ->where('student_id', $student->id)
            ->first();

        if (!$attendance) {
            abort(403);
        }

        $teacherName = null;
        if ($attendance->teacher_id) {
            $t = Teacher::find($attendance->teacher_id);
            if ($t) {
                $teacherName = trim($t->first_name . ' ' . ($t->middle_name ?? '') . ' ' . $t->last_name);
            }
        }

        $classmates = Student::query()
            ->join('attendances', 'attendances.student_id', '=', 'students.id')
            ->where('attendances.course_id', $course->id)
            ->where('attendances.section', $attendance->section)
            ->select('students.*')
            ->with(['degree', 'userAccount'])
            ->paginate(10);

        return view('portal_student_course_details', [
            'course' => $course,
            'attendance' => $attendance,
            'teacherName' => $teacherName,
            'classmates' => $classmates,
        ]);
    }
    // public function login(Request $request){
    //     if ($request->isMethod('post')) {
    //         $user_name = $request->input('username');
    //         $pass_word = $request->input('password');
            
    //         if (!$user_name || !$pass_word) {
    //             return back()->with('msg', 'Please provide username and password');
    //         }

    //         $user = UserAccount::where('username', $user_name)->first();

    //         if ($user && Hash::check($pass_word, $user->password)) {
    //             // $request->session()->put('user_id', $user->id);
    //             // $redirectUrl = url("/studentDashboard/{$user->id}/edit");
    //             // $msg = 'Login successful. Redirecting to students landing page...';
    //             // return view('portal_login_success')->with('redirectUrl', $redirectUrl)->with('msg', $msg);
    //             // session([
    //             // "logged_user" => $user->username,
    //             // "logged_id" => $user->id,
    //             // "logged_role" => $user->role,
    //             // ]);
    //             Session::put('logged_user', $user->username);
    //             Session::put('logged_id', $user->id);
    //             Session::put('logged_role', $user->role);

    //             if ($user->role === 'student') {
    //                 return redirect('/studentDashboard');
    //             } elseif ($user->role === 'teacher') {
    //                 return redirect('/student');
    //             } elseif ($user->role === 'admin') {
    //                 return redirect('/student');
    //             }
    //         } else {
    //             // return back()->with('msg', 'Invalid username or password');
    //             $msg = 'Invalid username or password';
    //             Session::forget('logged_user');
    //             Session::flush();
    //             return view('portal_login_page')->with('msg', $msg);
    //         }
    //     }

    //     return view('portal_login_page');
    // }
    public function login(Request $request)
{
    if ($request->isMethod('post')) {
        $user_name = $request->input('username');
        $pass_word = $request->input('password');
        
        if (!$user_name || !$pass_word) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Please provide username and password'], 422);
            }
            return back()->with('msg', 'Please provide username and password');
        }

        $user = UserAccount::where('username', $user_name)->first();

        if ($user && Hash::check($pass_word, $user->password)) {
            // Set session variables
            Session::put('logged_user', $user->username);
            Session::put('logged_id', $user->id);
            Session::put('logged_role', $user->role);

            // AJAX request: return role as JSON
            if ($request->expectsJson()) {
                if ($user->role === 'student' && $user->must_change_password) {
                    return response()->json([
                        'role' => $user->role,
                        'redirect' => route('studentDashboard.edit', $user->id),
                    ]);
                }
                $redirect = null;
                if ($user->role === 'student') {
                    $redirect = route('studentDashboard.index');
                } elseif ($user->role === 'teacher') {
                    $redirect = route('teacherDashboard.index');
                } elseif ($user->role === 'admin') {
                    $redirect = route('manageStudents');
                }

                return response()->json([
                    'role' => $user->role,
                    'redirect' => $redirect,
                ]);
            }

            // Normal form submission: redirect
            if ($user->role === 'student') {
                if ($user->must_change_password) {
                    return redirect()->route('studentDashboard.edit', $user->id);
                }
                return redirect('/studentDashboard');
            } elseif ($user->role === 'teacher') {
                return redirect()->route('teacherDashboard.index');
            } elseif ($user->role === 'admin') {
                return redirect('/student');
            }
        } else {
            // Authentication failed
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Invalid username or password'], 401);
            }
            $msg = 'Invalid username or password';
            Session::forget('logged_user');
            Session::flush();
            return view('portal_login_page')->with('msg', $msg);
        }
    }

    // GET request – show login page
    return view('portal_login_page');
}
    public function logout(Request $request)
    {
        Session::flush();
        return redirect('/')->with('msg', 'Logged out successfully.');
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
         $user = UserAccount::find($id);
      
        return view('portal_change_password', [
            'user' => $user,
            
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
          
        $user_account = UserAccount::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->route('studentDashboard.edit', $user_account->id)
                ->withErrors($validator)
                ->withInput();
        }

        // Verify old password
        if (!Hash::check($request->input('old_password'), $user_account->password)) {
            $key = 'pwd_change_attempts_' . $user_account->id;
            $attempts = session($key, 0) + 1;
            session([$key => $attempts]);

            if ($attempts >= 3) {
                // exceed max attempts: clear session and force login
                Session::forget('logged_id');
                session()->forget($key);
                return redirect('/')->with('msg', 'Maximum password attempts exceeded. Please login again.');
            }

            return redirect()->route('studentDashboard.edit', $user_account->id)
                ->withErrors(['old_password' => 'Old password is incorrect.'])
                ->with('msg', "Attempt {$attempts} of 3")
                ->withInput();
        }

        // Update password
        $user_account->password = Hash::make($request->input('password'));
        $user_account->must_change_password = 0;
        $user_account->save();

        // reset attempt counter on success
        $key = 'pwd_change_attempts_' . $user_account->id;
        session()->forget($key);

        $msg1 = "Password updated successfully!";
        Log::info($msg1 . ' user_id=' . $user_account->id);

        $role = Session::get('logged_role');
        if ($role === 'teacher') {
            return redirect()->route('teacherDashboard.index')->with('message', $msg1);
        }
        if ($role === 'admin') {
            return redirect()->route('manageStudents')->with('message', $msg1);
        }

        return redirect()->route('studentDashboard.index')->with('message', $msg1);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
