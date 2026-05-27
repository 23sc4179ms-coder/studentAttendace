<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Models\Teacher;
use App\Models\UserAccount;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    /**
     * AJAX endpoint: returns teacher details as JSON for view modal.
     */
    public function showJson(string $id)
    {
        $teacher = Teacher::with('userAccount')->findOrFail($id);

        $imagePath = $teacher->image_path;
        if (empty($imagePath)) {
            $dir = public_path('uploads/teachers');
            if (is_dir($dir)) {
                $matches = glob($dir . '/SN-' . $teacher->id . '-*.*') ?: [];
                if (!empty($matches)) {
                    // Prefer the most recently modified file
                    usort($matches, function ($a, $b) {
                        return filemtime($b) <=> filemtime($a);
                    });
                    $imagePath = 'uploads/teachers/' . basename($matches[0]);
                }
            }
        }

        return response()->json([
            'first_name' => $teacher->first_name,
            'middle_name' => $teacher->middle_name,
            'last_name' => $teacher->last_name,
            'email' => optional($teacher->userAccount)->email ?? $teacher->email,
            'contact_no' => $teacher->contact_no,
            'image_url' => $imagePath ? asset($imagePath) : '',
        ]);
    }
    /**
     * Display a listing of the teachers.
     */
    public function index()
    {
        return redirect()->route('manageStudents');
    }

    /**
     * AJAX endpoint: returns the teachers table partial.
     */
    public function list()
    {
        $teachers = Teacher::paginate(5);
        return view('portal_teacher_list', compact('teachers'));
    }

    /**
     * Show form to create a new teacher.
     */
    public function create()
    {
        return view('portal_addteacher');
    }

    /**
     * Store a newly created teacher and user account.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|min:2',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|min:2',
            'email' => 'required|email|unique:user_accounts,email',
            'contact_no' => 'nullable|min:7',
            'username' => 'required|unique:user_accounts,username',
            'password' => 'required|min:8',
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

        $teacher = DB::transaction(function () use ($request) {
            $user = UserAccount::create([
                'username' => $request->input('username'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'role' => 'teacher',
                'is_active' => 1,
            ]);

            return Teacher::create([
                'user_account_id' => $user->id,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'contact_no' => $request->contact_no,
            ]);
        });

        if ($request->hasFile('profile_image')) {
            try {
                $file = $request->file('profile_image');
                $year = now()->year;
                $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
                $filename = "SN-{$teacher->id}-{$year}.{$ext}";
                $relativeDir = 'uploads/teachers';
                $absoluteDir = public_path($relativeDir);

                File::ensureDirectoryExists($absoluteDir);
                $file->move($absoluteDir, $filename);

                $teacher->image_path = $relativeDir . '/' . $filename;
                $teacher->save();
            } catch (\Throwable $e) {
                Log::error('Teacher image upload failed', [
                    'teacher_id' => $teacher->id,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Teacher created: ' . $request->first_name . ' ' . $request->last_name);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Teacher created successfully!', 'teacher' => $teacher], 201);
        }

        return redirect()->route('manageStudents')->with('message', 'Teacher created successfully!');
    }

    /**
     * Display the specified teacher.
     */
    public function show(string $id)
    {
        $teacher = Teacher::findOrFail($id);
        return view('portal_teacher_details', compact('teacher'));
    }

    /**
     * Show the form for editing the specified teacher.
     */
    public function edit(string $id)
    {
        $teacher = Teacher::with('userAccount')->findOrFail($id);
        return view('portal_editteacher', compact('teacher'));
    }

    /**
     * Update the specified teacher in storage.
     */
    public function update(Request $request, string $id)
    {
        $teacher = Teacher::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|min:2',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|min:2',
            'email' => [
                'required',
                'email',
                Rule::unique('teachers', 'email')->ignore($teacher->id),
                Rule::unique('user_accounts', 'email')->ignore($teacher->user_account_id ?? 0),
            ],
            'contact_no' => 'nullable|min:7',   // Consistent with store()
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

        // Update teacher details
        $teacher->first_name = $request->first_name;
        $teacher->middle_name = $request->middle_name;
        $teacher->last_name = $request->last_name;
        $teacher->email = $request->email;
        $teacher->contact_no = $request->contact_no;
        $teacher->save();

        if ($request->hasFile('profile_image')) {
            $oldPath = $teacher->image_path;
            if (!empty($oldPath) && str_starts_with($oldPath, 'uploads/teachers/')) {
                $absoluteOld = public_path($oldPath);
                if (is_file($absoluteOld)) {
                    @unlink($absoluteOld);
                }
            }

            try {
                $file = $request->file('profile_image');
                $year = now()->year;
                $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
                $filename = "SN-{$teacher->id}-{$year}.{$ext}";
                $relativeDir = 'uploads/teachers';
                $absoluteDir = public_path($relativeDir);

                File::ensureDirectoryExists($absoluteDir);
                $file->move($absoluteDir, $filename);

                $teacher->image_path = $relativeDir . '/' . $filename;
                $teacher->save();
            } catch (\Throwable $e) {
                Log::error('Teacher image update failed', [
                    'teacher_id' => $teacher->id,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        // Keep linked user account email in sync
        $user = $teacher->userAccount;
        if ($user && $user->email !== $request->email) {
            $user->email = $request->email;
            $user->save();
        }

        Log::info('Teacher updated: ' . $teacher->id);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Teacher updated successfully!',
                'teacher' => $teacher,
                'image_url' => $teacher->image_path ? asset($teacher->image_path) : '',
            ]);
        }

        return redirect()->route('manageStudents')->with('message', 'Teacher updated successfully!');
    }

    /**
     * Remove the specified teacher from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $teacher = Teacher::findOrFail($id);
        $name = trim($teacher->first_name . ' ' . ($teacher->middle_name ?? '') . ' ' . $teacher->last_name);

        try {
            DB::transaction(function () use ($teacher) {
                $user = $teacher->userAccount;

                // IMPORTANT: delete teacher first, then delete user account.
                // The teachers.user_account_id FK prevents deleting user_accounts while teacher exists.
                $teacher->delete();

                if ($user) {
                    $user->delete();
                }
            });

            Log::info('Teacher deleted: ' . $name);
        } catch (QueryException $e) {
            Log::error('Teacher delete failed: ' . $name, [
                'teacher_id' => $teacher->id,
                'error' => $e->getMessage(),
            ]);

            $message = 'Cannot delete teacher because it is linked to other records.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => $message], 409);
            }

            return redirect()->route('manageStudents')->with('message', $message);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Teacher deleted successfully!']);
        }

        return redirect()->route('manageStudents')->with('message', 'Teacher deleted successfully!');
    }
}