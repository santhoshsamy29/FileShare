<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
	protected $fillable = ['name','size','course_id'];
	protected $hidden=['updated_at'];
}
