<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable=['notification_text','course_id'];
    protected $hidden=['updated_at'];
}
