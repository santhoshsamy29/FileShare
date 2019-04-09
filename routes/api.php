<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('teacher/register', 'TeacherController@register');
Route::post('teacher/login', 'TeacherController@login');
Route::post('teacher/course','TeacherController@getCourseFromTeacherId');

Route::post('student/register','StudentController@register');
Route::post('student/login','StudentController@login');

Route::post('file/upload','FileController@upload');
Route::post('file/show','FileController@show');

Route::post('file/chumma','FileController@getTeacherNameFromCourseId');

Route::get('course/show','CourseController@show');

Route::post('enroll','EnrollmentController@enroll');
Route::post('enroll/display','EnrollmentController@display');
Route::post('enroll/course','EnrollmentConroller@getCourseNameFromCourseId');
Route::post('enroll/teacher','EnrollmentConroller@getTeacherNameFromCourseId');
Route::post('enroll/student','EnrollmentConroller@getStudentNameFromStudentId');


//Route::post('file/show_single','FileController@show-single');


