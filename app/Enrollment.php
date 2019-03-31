<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $fillable = ['course_id', 'student_id'];
    protected $hidden=['created_at','updated_at'];

}
