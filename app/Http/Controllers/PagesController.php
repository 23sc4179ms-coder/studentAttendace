<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Student;

class PagesController extends Controller
{
   public function userProfile() {
    $user = User::find(1); 
    // echo $user->profile->bio;
    echo $user->name." - ".$user->profile->bio;
   }
   public function userPosts() {
    $user = User::find(1); 
    foreach($user->posts as $post) {
        echo "$user->name: $post->content - $post->title<br>";
    }
   }
   public function studentCourses() {
        $student = Student::find(6);
        if (! $student) {
            return response('Student not found', 404);
        }
        foreach($student->courses as $course) {
            echo "$student->last_name is enrolled in $course->course_name<br>";
        }
    
   }
   public function maintenance(){
        return response()->view('maintenace', [], 503);
    }
    
    public function demo() {
        return view('demo');
    }
}
