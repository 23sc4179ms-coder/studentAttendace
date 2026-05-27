<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class course_student extends Model
{
    //
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
    
}

