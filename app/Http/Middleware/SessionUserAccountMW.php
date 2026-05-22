<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;
use App\Models\UserAccount;
class SessionUserAccountMW
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user_role = Session::get('logged_role');
        if (!$user_role) {
            return redirect('/')->with('msg', 'Please log in to access this page.');
        }
        $segments = $request->segments();
        $first = $segments[0] ?? '';

        // Role-based access restrictions
        if ($user_role === 'student') {
            // Force a one-time password change before accessing the dashboard
            $userId = Session::get('logged_id');
            if ($userId) {
                $user = UserAccount::find($userId);
                if ($user && $user->must_change_password) {
                    $isChangePasswordRoute = ($first === 'studentDashboard' && ($segments[2] ?? '') === 'edit')
                        || ($first === 'studentDashboard' && in_array($request->method(), ['PUT', 'PATCH']));

                    if (!$isChangePasswordRoute) {
                        return redirect()->route('studentDashboard.edit', $userId);
                    }
                }
            }

            // Students should not access admin/teacher management pages
            if (in_array($first, ['teacher', 'manageStudents', 'teacherDashboard', 'course', 'degree'])) {
                return redirect('/studentDashboard')->with('msg', 'Access denied.');
            }
            // Prevent students from using student management actions (create/edit/delete)
            if ($first === 'student' && isset($segments[1]) && in_array($segments[1], ['create', 'edit', 'destroy'])) {
                return redirect('/studentDashboard')->with('msg', 'Access denied.');
            }
            // Block destructive HTTP verbs on /student routes
            if ($first === 'student' && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                return redirect('/studentDashboard')->with('msg', 'Access denied.');
            }

        }

        if ($user_role === 'teacher') {
            if (in_array($first, ['studentDashboard'])) {
                return redirect('/teacherDashboard')->with('msg', 'Access denied.');
            }
            if (in_array($first, ['enrollstudent'])) {
                return redirect('/teacherDashboard')->with('msg', 'Access denied.');
            }
        }
        if ($user_role === 'admin') {
            if (in_array($first, ['studentDashboard'])) {
                return redirect('/student')->with('msg', 'Access denied.');
            }
        }

        if ($user_role === 'student') {
            if (in_array($first, ['enrollstudent'])) {
                return redirect('/studentDashboard')->with('msg', 'Access denied.');
            }
        }

        // admins have full access

        return $next($request);
    }
}
