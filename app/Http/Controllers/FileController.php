<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\File;
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
