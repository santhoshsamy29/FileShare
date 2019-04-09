<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\File;
use \App\Enrollment;
use \App\Student;
use Validator;

class FileController extends Controller
{
    

    public function upload(Request $request){
 
        $validator = Validator::make($request->all(),[
            'file' => 'max:1999',
            'course_id' => 'required|max:127'
        ]);

        if($validator->fails()){

            return response([
                'data' => 'File size too large',
                'status' => 413
            ]);
        } else {
            if($request->hasFile('file')){

                $fileNameWithExt = $request->file('file')->getClientOriginalName();
                $fileSize = $request->file('file')->getClientSize();
                $fileSizeAsString = strval($fileSize);
                $fileName = pathinfo($fileNameWithExt , PATHINFO_FILENAME);
                $extension = $request->file('file')->getClientOriginalExtension();
                $fileNameToStore = $fileName.'_'.time().'.'.$extension;

    
                $request->file('file')->storeAs('public/files',$fileNameToStore);

                $file = File::create([
                    'name' => $fileNameToStore,
                    'size' => $fileSize,
                    'course_id' => $request->input('course_id')
                ]);


                //Send notif

                

                $course_id = $request->input('course_id');
    
                $student_ids = Enrollment::select('student_id')->where('course_id','=',$course_id)->get();


                $en = json_decode($student_ids, true);


              
                foreach ($en as $key => $value) {
                    $token = Student::select('token')->where('id','=',$value["student_id"])->first()->token;

                    $this->sendNotification($token, $fileNameWithExt);
                }

                $errors =  "File uploaded";
                    return response(["data" => $errors,
                    "status" => 200]);
            } else {
                $errors[]=['title' => "Filenotfound"];
                return response(["data" => $errors,
                    "status" => 500]);
            }
        }
    }

    public function sendNotification($token, $fileNameWithExt) {

        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

        $notification = [
            'title' => "A new file has been uploaded",
            'body' => $fileNameWithExt,
            'sound' => true,
        ];

        // $token = "efoz2GVw4kA:APA91bF95h6qixrBq5nySROB2N0pXEuiPOmIdZn-8jrx8svm965Z9kQ6wiWte5z8CkPWNSlTToLO6w_SGvwtfyz8hUDJ2AWDgYDgqMN5b7ZWdHmJOftnIy7VNeA2f38_PJjWG0UjqhV8";
        
        $extraNotificationData = ["message" => $notification,"moredata" =>'dd'];

        $fcmNotification = [
            //'registration_ids' => $tokenList, //multple token array
            'to'        => $token, //single token
            'notification' => $notification,
            'data' => $extraNotificationData
        ];

        $headers = [
            'Authorization: key= AIzaSyBje6WHFbZh2YYW3KtgBDiLgHRaeKw0GHY',
            'Content-Type: application/json'
        ];


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);

        info("sent");
      
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
               
                
               //jnjlo

                $this->sendNotification();


                $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
                //$token=$token;
        
                $notification = [
                    'title' => "hello title",
                    "body" => "This is an FCM notification message!",
                    'sound' => true,
                ];
        
                $token = "efoz2GVw4kA:APA91bF95h6qixrBq5nySROB2N0pXEuiPOmIdZn-8jrx8svm965Z9kQ6wiWte5z8CkPWNSlTToLO6w_SGvwtfyz8hUDJ2AWDgYDgqMN5b7ZWdHmJOftnIy7VNeA2f38_PJjWG0UjqhV8";
                
                $extraNotificationData = ["message" => $notification,"moredata" =>'dd'];
        
                $fcmNotification = [
                    //'registration_ids' => $tokenList, //multple token array
                    'to'        => $token, //single token
                    'notification' => $notification,
                    'data' => $extraNotificationData
                ];
        
                $headers = [
                    'Authorization: key= AIzaSyBje6WHFbZh2YYW3KtgBDiLgHRaeKw0GHY',
                    'Content-Type: application/json'
                ];
        
        
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,$fcmUrl);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
                $result = curl_exec($ch);
                curl_close($ch);
        
                info("sent");







                //$students_token = Student::select('token')->where('id','=',$teacher_id)->first()->name;
              
                return response([
                    'data' => "hi",
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

    

    public function show(Request $request){


       
        try
		{ 

            $validator = Validator::make($request->all(),[
                'course_id' => 'required|max:127'
            ]);
    
            if($validator->fails()){

                return response([
                    'data' => 'Invalid or missing parameters',
                    'status' => 400
                ]);
            } else {
                $course_id = $request->input('course_id');


                $files=File::where('course_id','=',$course_id)->get();
                return  response([
                    'data' => $files,
                    'status' => 200,
                ]);  
            }


           
		}

		catch(Exception $error){
			$errors = $error->getMessage();
			return response([
                "data" => $errors,
                "status" => 500
            ]);
		}       
    }
}
