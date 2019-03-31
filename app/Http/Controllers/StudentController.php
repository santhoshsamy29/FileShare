<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Validator;
use Illuminate\Http\Request;
use \App\Student;
use \App\Enrollment;

class StudentController extends Controller
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

                $check_student = Student::where('email','=', $request->input('email'))->first();
                if($check_student){
                    $message = "This email has already been registered";
                    
                    return response([
                        "data" => $message, 
                        "status" => 200
                    ]);
                } else {
                    $student=Student::create([
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
				$student = Student::where('email','=',$email)->first();
				if($student)
				{
					if(password_verify($password,$student->password))
					{
						$message = strval(Student::select('id')->where('email','=', $email)->first()->id);
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
					$errors='Student not registered';
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

}
