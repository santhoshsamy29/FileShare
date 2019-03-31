<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Course;
use \App\Teacher;


class CourseController extends Controller
{
    public function show(Request $request){

        try{

            

                $ids = Course::select('id')->get();
                
                




                $en = json_decode($ids, true);


              
                foreach ($en as $key => $value) {

                    info($key."  ".$value["id"]);

                    $course_name = Course::select('course_name')->where('id','=',$value["id"])->first()->course_name;

                    $teacher_id = Course::select('teacher_id')->where('id','=',$value)->first()->teacher_id;
                    $teacher_name = Teacher::select('name')->where('id','=',$teacher_id)->first()->name;

                    $mobj = new \stdClass();
                    $mobj->course_id = $value["id"];
                    $mobj->teacher_name = $teacher_name;
                    $mobj->course_name = $course_name; 

                    $course_list[$key] = $mobj ;
                   
                }
              
                return response([
                    'data' => $course_list,
                    'status' => 200
                ]);




        } catch (Exception $error){
            $errors= $error->getMessage();
			return  response([
				'data' => $errors,
				'status' => 500,
			]);
        }
    }
}
