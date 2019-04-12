<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use \App\Notification;
use \App\Enrollment;
use \App\Student;

class NotificationController extends Controller
{
    

    public function store(Request $request) {

        try{
			$validator = Validator::make($request->all(),[
                'notification_text' => 'required',
				'course_id' => 'required|max:127'
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
                $course_id = $request->input('course_id');
                $notification_text = $request->input('notification_text');
            

                $notification = Notification::create([
                    'notification_text' => $notification_text,
                    'course_id' => $course_id
                ]);

                $course_id = $request->input('course_id');
                $student_ids = Enrollment::select('student_id')->where('course_id','=',$course_id)->get();
                $en = json_decode($student_ids, true);
              
                foreach ($en as $key => $value) {
                    $token = Student::select('token')->where('id','=',$value["student_id"])->first()->token;

                    $this->sendNotification($token, $notification_text);
                }

                        
				return  response([
					'data' => "Notification Sent",
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


    public function sendNotification($token, $notification_text) {

        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

        $notification = [
            'title' => "A new file has been uploaded",
            'body' => $notification_text,
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


    public function fetch(Request $request){


        try{

            $validator = Validator::make($request->all(),[
                'course_id' => 'required'
            ]);

            if($validator->fails()){
                
                return response([
                    'data' => "Invalid or missing params",
                    'status' => 400
                ]); 
            } else {

                $course_id = $request->input('course_id');

                $notifications = Notification::where('course_id','=',$course_id)->get();

                return response([
                    'data' => $notifications,
                    'status' => 200
                ]);
            }

        } catch (Exception $error){

            return response([
                'data' => $error->getMessage(),
                'status' => 500
            ]);
        }

       
    }
}
