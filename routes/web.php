<?php

use App\Models\Course;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    dd(Course::find(1)->students[0]);
    // dd(Student::find(1)->user);
    // dd(User::find(2)->teacher->courses[0]->students->count());
    return view('welcome');
});
