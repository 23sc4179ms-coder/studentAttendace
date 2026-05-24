<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Degree extends Model
{
    protected $fillable = [
        'degree_name',
    ];

    /**
     * @return HasMany<Student, Degree>
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'degree_id');
    }

    /**
     * @return HasMany<Teacher, Degree>
     */
    public function teachers()
    {
        return $this->hasMany(Teacher::class, 'degree_id');
    }
}
