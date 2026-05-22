<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class UserAccount extends Model
{
    //
    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'is_active',
        'must_change_password'
    ];
    public function student(){
        return $this->hasOne(Student::class,'user_account_id');
    }
    public function teacher(){
        return $this->hasOne(teacher::class,'user_account_id');
    }
}
