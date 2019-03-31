<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Validator;
use Illuminate\Http\Request;
use \App\Teacher;
use \App\Course;

class TeacherController extends Controller
{
    

    public function register(Request $request) {

        try{
            $validator = Validator::make($request->all(),[
                'name' => 'required|max:127',
                'email' => 'required|email|max:127',
                'password' => 'required|max:127'
            ]);
            if($validator->fails())
            {
                $errors = 'Invalid or Missing Parameters';
            
                return response([
                    "data" => $errors,
                    "status" => 400
                ]);
            }
            else
            {

                $check_teacher = Teacher::where('email','=', $request->input('email'))->first();

                if($check_teacher){

                    $message="This email has already been registered";
                    
                    return response([
                        "data" => $message, 
                        "status" => 200
                    ]);

                } else {
                    $teacher=Teacher::create([
                        'name' => $request->input('name'),
                        'email' => $request->input('email'),
                        'password' => password_hash($request->input('password'),PASSWORD_DEFAULT)
                    ]);
                    $message="You have been registered";
                    
                    return response([
                        "data" => $message, 
                        "status" => 200
                    ]);
                }
            }
        } catch(Exception $error){
            $errors = $error->getMessage();
            return response([
                "data" => $errors,
                "status" => 500
            ]);
        }    
    }



    public function login(Request $request)
	{
		try{
			$validator = Validator::make($request->all(),[
				'email' => 'required|max:127',
				'password' => 'required|max:127'
			]);
			if($validator->fails())
			{
				$errors='Invalid or Missing Parameters';
				return  response([
					'data' => $errors,
					'status' => 400,
				]);
			}
			else
			{
				$email = $request->input('email');
				$password = $request->input('password');
				$teacher = Teacher::where('email','=',$email)->first();
				if($teacher)
				{
					if(password_verify($password,$teacher->password))
					{
						$message = strval(Teacher::select('id')->where('email','=', $email)->first()->id);
                        $status_code = 200;
                        
						return  response([
							'data' => $message,
							'status' => 200,
						]);   	
					}
					else
					{
						$errors='Invalid Credentials';
						return  response([
							'data' => $errors,
							'status' => 401,
						]);		
					}
				}
				else
				{
					$errors='Staff not registered';
					return  response([
						'data' => $errors,
						'status' => 400,
					]);
				}
			}
		}
		catch(Exception $error)
		{
			$errors= $error->getMessage();
			return  response([
				'data' => $errors,
				'status' => 500,
			]);
		}
	}

	public function getCourseFromTeacherId(Request $request){


		try{
			$validator = Validator::make($request->all(),[
				'teacher_id' => 'required|max:127',
			]);
			if($validator->fails())
			{
				$errors='Invalid or Missing Parameters';
				return  response([
					'data' => $errors,
					'status' => 400,
				]);
			}
			else
			{
				$teacher_id = $request->input('teacher_id');
			
				$course = Course::where('teacher_id','=',$teacher_id)->get();
				
				
						$message =  $course;
                        $status_code = 200;
                        
						return  response([
							'data' => $message,
							'status' => 200,
						]);   	
					
				
				
			}
		}
		catch(Exception $error)
		{
			$errors= $error->getMessage();
			return  response([
				'data' => $errors,
				'status' => 500,
			]);
		}


	}

}
