<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Student extends Model
{
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'contact_no',
        'degree_id',
        'user_account_id',
        'image_path'
    ];

    /**
     * @return BelongsTo<Degree, Student>
     */
    public function degree() 
    {
        return $this->belongsTo(Degree::class, 'degree_id');
    }
    public function courses()
    {
    return $this->belongsToMany(Course::class, 'course_students', 'student_id', 'course_id')
        ->withPivot('teacher_id')
        ->withTimestamps();
    }
    public function userAccount(){
        return $this->belongsTo(UserAccount::class,'user_account_id');
    }
}
