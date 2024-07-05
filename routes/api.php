<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\AdminController;


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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/email/verify', [AuthController::class, 'emailVerify'])->name('emailVerify');
    Route::prefix('student')->group(function () {
        Route::post('/register', [AuthController::class, 'studentRegister'])->name('studentRegister');

        Route::middleware(['auth:sanctum', 'abilities:student'])->group(function () {
            Route::get('get-profile', [StudentController::class, 'getProfile']);
            Route::get('courses', [StudentController::class, 'getCourses']);
            Route::get('my-courses', [StudentController::class, 'myCourses']);
            Route::get('get-module/{uuid}', [StudentController::class, 'getModule']);
            Route::get('course/{id}', [StudentController::class, 'getCourse']);
            Route::post('join-course', [StudentController::class, 'joinCourse']);
            Route::get('get-schedules', [StudentController::class, 'getSchedules']);
            Route::get('upcoming-schedules', [StudentController::class, 'upcomingSchedules']);
        });
    });

    Route::prefix('teacher')->group(function () {
        Route::post('/register', [AuthController::class, 'teacherRegister'])->name('teacherRegister');
        Route::middleware(['auth:sanctum', 'abilities:teacher'])->group(function () {
            Route::get('get-profile', [TeacherController::class, 'getProfile']);
            Route::post('create-course', [TeacherController::class, 'createCourse']);
            Route::get('course/{id}', [TeacherController::class, 'getCourse']);
            Route::get('courses', [TeacherController::class, 'getCourses']);
            Route::get('get-kpi', [TeacherController::class, 'getKpi']);
            Route::get('get-students', [TeacherController::class, 'getStudents']);
            Route::post('add-schedule', [TeacherController::class, 'addSchedule']);
            Route::get('get-schedules', [TeacherController::class, 'getSchedules']);
            Route::get('get-reminders', [TeacherController::class, 'getReminders']);
            Route::post('update-course', [TeacherController::class, 'updateCourse']);
            Route::post('update-module', [TeacherController::class, 'updateModule']);
            Route::post('add-module', [TeacherController::class, 'addModule']);
            Route::post('delete-module', [TeacherController::class, 'deleteModule']);
            Route::post('delete-video', [TeacherController::class, 'deleteVideo']);
            Route::post('delete-document', [TeacherController::class, 'deleteDocument']);
            Route::post('add-video', [TeacherController::class, 'addVideo']);
            Route::post('add-document', [TeacherController::class, 'addDocument']);
        });
    });

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('update-profile', [UserController::class, 'updateProfile']);
        Route::get('activities',  [UserController::class, 'activities']);
    });

    Route::prefix('/admin')->group(function () {
        Route::post('/login', [AuthController::class, 'adminLogin']);

        Route::middleware(['auth:sanctum'])->group(function () {
            Route::get('kpi', [AdminController::class, 'kpi']);
            Route::get('students', [AdminController::class, 'students']);
            Route::get('teachers', [AdminController::class, 'teachers']);
            Route::get('chats', [AdminController::class, 'chats']);
            Route::post('save-chat', [AdminController::class, 'saveChat']);
            Route::post('message-students', [AdminController::class, 'sendMessageToAllStudents']);
            Route::post('message-teachers', [AdminController::class, 'sendMessageToAllTeachers']);
        });
    });
});
