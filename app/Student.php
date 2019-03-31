<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable=['name','email','password'];
    protected $hidden=['created_at','updated_at'];
}
