<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseEnrolled extends Model
{
    protected $table = 'course_enrolled';

    protected $fillable = [
        'course_id',
        'student_id',
        'teacher_id',
        'section',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }
}
