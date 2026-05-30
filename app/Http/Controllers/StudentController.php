<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Degree;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\UserAccount;
use App\Models\Course;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
class StudentController extends Controller
{
    
    public function showJson($id)
{
    $student = Student::with('degree', 'userAccount')->findOrFail($id);

        $imagePath = $student->image_path;
        if (empty($imagePath)) {
            $dir = public_path('uploads/students');
            if (is_dir($dir)) {
                $matches = glob($dir . '/SN-' . $student->id . '-*.*') ?: [];
                if (!empty($matches)) {
                    usort($matches, function ($a, $b) {
                        return filemtime($b) <=> filemtime($a);
                    });
                    $imagePath = 'uploads/students/' . basename($matches[0]);
                }
            }
        }

    return response()->json([
        'first_name'  => $student->first_name,
        'middle_name' => $student->middle_name,
        'last_name'   => $student->last_name,
        'email'       => $student->userAccount?->email ?? '',
        'contact_no'  => $student->contact_no,
        'degree_name' => $student->degree?->degree_name ?? '',
            'image_url'   => $imagePath ? asset($imagePath) : '',
    ]);
}

    public function studentAbout() {
    return view("student_about");
    }

    public function studentHome() {
    return view("student_home");
    }

    public function studentPage() {
    return view("student_page");
    }
    public function studentDashboard() {
    $logged_user = Session::get('logged_user');
    $logged_role = Session::get('logged_role');
    return view('student_dashboard')->with('logged_user', $logged_user)->with('logged_role', $logged_role);
    }
    public function manageStudents() {
        $students = Student::with(['degree', 'userAccount'])->paginate(5);
        $degrees = Degree::orderBy('degree_name')->get();
        $teachers = Teacher::paginate(5);
        $courses = Course::orderBy('course_name')->get();
        $allTeachers = Teacher::orderBy('first_name')->orderBy('last_name')->get();
        $logged_user = Session::get('logged_user');
        $logged_role = Session::get('logged_role');
        return view('student', compact('students', 'degrees', 'teachers', 'courses', 'allTeachers', 'logged_user', 'logged_role'));
    }
    public function index()
    {
        $students = Student::with(['degree', 'userAccount'])->paginate(5);
        $degrees = Degree::orderBy('degree_name')->get();
        $logged_user = Session::get('logged_user');
        $logged_role = Session::get('logged_role');

          if($logged_role === 'admin'){
            return redirect('manageStudents');
          }
                    elseif ($logged_role === 'teacher') {
                        $teachers = Teacher::paginate(5);
                        $courses = Course::orderBy('course_name')->get();
                        $allTeachers = Teacher::orderBy('first_name')->orderBy('last_name')->get();
                        return view('student', compact('students', 'degrees', 'teachers', 'courses', 'allTeachers', 'logged_user', 'logged_role'));
                }
           else {
            return view('student_dashboard')->with('students', $students)->with('degrees', $degrees)
            ->with('logged_user', $logged_user)->with('logged_role', $logged_role);
        }

       
        
        
    }

    
    public function list()
    {
        $students = Student::with(['degree', 'userAccount'])->paginate(5);
        return view('student_list', compact('students'));
    }

       
        

    

    
    public function create()
    {
        $degrees = Degree::orderBy('degree_name')->get();
        return view('addstudent', [
            'degrees' => $degrees,
        ]);
    }

    
    public function store(Request $request)
    {

            $validator = Validator::make($request->all(), [
                'first_name' => 'required|min:2',
                'last_name' => 'required|min:2',
                'email' => 'required|email|unique:user_accounts,email',

                'contact_no' => 'required|min:11',
                'degree_id' => 'required|exists:degrees,id',
                'username'=>'required|unique:user_accounts,username',
                'password'=>'required|min:8',
                'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

                    if ($validator->fails()) {
                        if ($request->ajax() || $request->wantsJson()) {
                            return response()->json(['errors' => $validator->errors()], 422);
                        }

                        return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
                    }
        try {
            $student = DB::transaction(function () use ($request) {
                $userData = [
                    'username' => $request->input('username'),
                    'email' => $request->input('email'),
                    'password' => Hash::make($request->input('password')),
                    'role' => 'student',
                    'is_active' => 1,
                ];

                if (Schema::hasColumn('user_accounts', 'must_change_password')) {
                    $userData['must_change_password'] = 1;
                }

                $user = UserAccount::create($userData);

                return Student::create([
                    'user_account_id' => $user->id,
                    'first_name' => $request->first_name,
                    'middle_name' => $request->middle_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'contact_no' => $request->contact_no,
                    'degree_id' => $request->degree_id,
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('Student create failed', [
                'message' => $e->getMessage(),
                'exception' => $e instanceof \Throwable ? $e->getTraceAsString() : null,
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Failed to create student. Check server logs.',
                    'error' => $e->getMessage(),
                    'trace' => str_split($e->getTraceAsString(), 1000)
                ], 500);
            }

            return redirect()->back()
                ->withErrors(['error' => 'Failed to create student.'])
                ->withInput();
        }

        if ($request->hasFile('profile_image')) {
            try {
                $file = $request->file('profile_image');
                $year = now()->year;
                $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
                $filename = "SN-{$student->id}-{$year}.{$ext}";
                $relativeDir = 'uploads/students';
                $absoluteDir = public_path($relativeDir);

                File::ensureDirectoryExists($absoluteDir);
                $file->move($absoluteDir, $filename);

                $student->image_path = $relativeDir . '/' . $filename;
                $student->save();
            } catch (\Throwable $e) {
                Log::error('Student image upload failed', [
                    'student_id' => $student->id,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        $msg = "Student created successfully!";
        Log::info($msg.$request->first_name);
               
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Student created successfully!', 'student' => $student], 201);
        }

        return redirect()->route('manageStudents')->with("message","Student created successfully!");
    
    }

    
    public function show(string $id)
    {
        $student = Student::with('degree')->findOrFail($id);
        return view('student_details')->with('student', $student);
    }

    
    public function edit(string $id)
    {
        $student = Student::with('userAccount')->findOrFail($id);
        $degrees = Degree::orderBy('degree_name')->get();

        return view('editstudent', [
            'student' => $student,
            'degrees' => $degrees,
            
        ]);

    }

    
        
            
        






    public function update(Request $request, string $id)
{
    $student = Student::findOrFail($id);
    
    $validator = Validator::make($request->all(), [
        'first_name' => 'required|min:2',
        'middle_name' => 'nullable|string|max:255',
        'last_name' => 'required|min:2',
            'email' => [
                'required',
                'email',
                Rule::unique('user_accounts', 'email')->ignore($student->user_account_id ?? 0),
            ],
        'contact_no' => 'required|min:11',
        'degree_id' => 'required|exists:degrees,id',
        'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    if ($validator->fails()) {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        return redirect()->route('student.edit', $student->id)
            ->withErrors($validator)
            ->withInput();
    }

    $validated = $validator->validated();

    $email = $validated['email'];
    if ($student->userAccount && $student->userAccount->email !== $email) {
        $student->userAccount->email = $email;
        $student->userAccount->save();
    }

    $student->fill([
        'first_name' => $validated['first_name'],
        'middle_name' => $validated['middle_name'] ?? null,
        'last_name' => $validated['last_name'],
                'email' => $email,
        'contact_no' => $validated['contact_no'],
        'degree_id' => $validated['degree_id'],
    ]);
    $student->save();

    if ($request->hasFile('profile_image')) {
        $oldPath = $student->image_path;
        if (!empty($oldPath) && str_starts_with($oldPath, 'uploads/students/')) {
            $absoluteOld = public_path($oldPath);
            if (is_file($absoluteOld)) {
                @unlink($absoluteOld);
            }
        }

        try {
            $file = $request->file('profile_image');
            $year = now()->year;
            $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
            $filename = "SN-{$student->id}-{$year}.{$ext}";
            $relativeDir = 'uploads/students';
            $absoluteDir = public_path($relativeDir);

            File::ensureDirectoryExists($absoluteDir);
            $file->move($absoluteDir, $filename);

            $student->image_path = $relativeDir . '/' . $filename;
            $student->save();
        } catch (\Throwable $e) {
            Log::error('Student image update failed', [
                'student_id' => $student->id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    $msg = "Student updated successfully!";
    Log::info($msg . ' ' . $student->first_name);

    if ($request->ajax() || $request->wantsJson()) {
        return response()->json([
            'message' => $msg,
            'student' => $student->load('degree', 'userAccount'),
            'image_url' => $student->image_path ? asset($student->image_path) : '',
        ]);
    }

    return redirect()->route('manageStudents')->with('message', $msg);
}

    
    public function destroy(string $id)
    {
        $student = Student::findOrFail($id);

        $name = trim($student->first_name . ' ' . ($student->middle_name ?? '') . ' ' . $student->last_name);
        $student->delete();

        $msg = "Student deleted successfully: {$name}";
        Log::info($msg);

        return redirect()->route('manageStudents')->with('message', $msg);

        
    }
}


