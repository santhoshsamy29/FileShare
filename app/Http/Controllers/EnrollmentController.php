<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use \App\Course;    
use \App\Enrollment;
use \App\Teacher;


class EnrollmentController extends Controller
{
    
    public function enroll(Request $request){


        try{
            $validator = Validator::make($request->all(),[
                'course_id' => 'required',
                'student_id' => 'required'
            ]);
    
            if($validator->fails()){
                return response([
                    'data' => 'Invalid or Missing parameters',
                    'status' => 400
                ]);
            } else {
                $course_id = $request->input('course_id');
                $student_id = $request->input('student_id');
    
                $matches = ['course_id' => $course_id, 'student_id' => $student_id];
    
                $enrollment = Enrollment::where($matches)->first();
    
                if($enrollment){
                    return response([
                        'data' => 'You have already enrolled',
                        'status' => 200
                    ]);
                } else {
                    $enroll = Enrollment::create([
                        'course_id' => $course_id,
                        'student_id' => $student_id
                    ]);
    
                    return response([
                        'data' => 'You have been successfully enrolled',
                        'status' => 200
                    ]);
                }
            }

        } catch (Exception $error){
            $errors= $error->getMessage();
			return  response([
				'data' => $errors,
				'status' => 500,
			]);
        }
    }

    public function display(Request $request){

        
        $course_list =array();


        try{
            $validator = Validator::make($request->all(),[
                'student_id' => 'required'
            ]);
    
            if($validator->fails()){
                return response([
                    'data' => 'Invalid or Missing parameters',
                    'status' => 400
                ]);
            } else {
               
                $student_id = $request->input('student_id');
                $enrollments = Enrollment::select('course_id')->where('student_id','=',$student_id)->get();


                $en = json_decode($enrollments, true);


              
                foreach ($en as $key => $value) {

                    info($key."  ".$value["course_id"]);

                    $course_name = Course::select('course_name')->where('id','=',$value["course_id"])->first()->course_name;

                    $teacher_id = Course::select('teacher_id')->where('id','=',$value)->first()->teacher_id;
                    $teacher_name = Teacher::select('name')->where('id','=',$teacher_id)->first()->name;

                    $mobj = new \stdClass();
                    $mobj->course_id = $value["course_id"];
                    $mobj->teacher_name = $teacher_name;
                    $mobj->course_name = $course_name; 

                    $course_list[$key] = $mobj ;
                   
                }
              
                return response([
                    'data' => $course_list,
                    'status' => 200
                ]);
                
            }


        } catch (Exception $error){
            $errors= $error->getMessage();
			return  response([
				'data' => $errors,
				'status' => 500,
			]);
        }
    }

    public function getStudentNameFromStudentId(Request $request){

        try{
            $validator = Validator::make($request->all(),[
                'student_id' => 'required'
            ]);
    
            if($validator->fails()){
                return response([
                    'data' => 'Invalid or Missing parameters',
                    'status' => 400
                ]);
            } else {
               
                $student_id = $request->input('student_id');
    
                $student_name = Student::select('name')->where('id','=',$student_id)->first()->name;
              
                return response([
                    'data' => $student_name,
                    'status' => 200
                ]);
                
            }


        } catch (Exception $error){
            $errors= $error->getMessage();
			return  response([
				'data' => $errors,
				'status' => 500,
			]);
        }

    }

    public function getTeacherNameFromCourseId(Request $request){


        try{
            $validator = Validator::make($request->all(),[
                'course_id' => 'required'
            ]);
    
            if($validator->fails()){
                return response([
                    'data' => 'Invalid or Missing parameters',
                    'status' => 400
                ]);
            } else {
               
                $course_id = $request->input('course_id');
    
                $teacher_id = Course::select('teacher_id')->where('id','=',$course_id)->first()->teacher_id;
                $teacher_name = Teacher::select('name')->where('id','=',$teacher_id)->first()->name;
              
                return response([
                    'data' => $teacher_name,
                    'status' => 200
                ]);
                
            }


        } catch (Exception $error){
            $errors= $error->getMessage();
			return  response([
				'data' => $errors,
				'status' => 500,
			]);
        }

    }



    public function getCourseNameFromCourseId(Request $request){


        try{
            $validator = Validator::make($request->all(),[
                'course_id' => 'required'
            ]);
    
            if($validator->fails()){
                return response([
                    'data' => 'Invalid or Missing parameters',
                    'status' => 400
                ]);
            } else {
               
                $course_id = $request->input('course_id');
    
                $course_name = Course::select('course_name')->where('id','=',$course_id)->first()->teacher_id;

                return response([
                    'data' => $course_name,
                    'status' => 200
                ]);
                
            }


        } catch (Exception $error){
            $errors= $error->getMessage();
			return  response([
				'data' => $errors,
				'status' => 500,
			]);
        }

    }
}
