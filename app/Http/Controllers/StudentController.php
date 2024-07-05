<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Jobs\ActivityJob;
use App\Services\NotificationService;

class StudentController extends Controller
{
    public function getProfile()
    {
        return response()->json(Auth::user(), 200);
    }

    public function getCourses()
    {
        $courses = Course::inRandomOrder()->paginate(20);
        return $courses;
    }

    public function myCourses()
    {
        return Auth::user()->student->courses;
    }

    public function getCourse($id)
    {
        $course = Course::where('uuid', $id)->first();
        if (!$course) {
            abort(400, 'Course not found');
        }

        return $course;
    }

    public function getModule($id)
    {
        $module = Module::where('uuid', $id)->first();
        if (!$module) {
            abort(400, 'Module not found');
        }

        return $module;
    }

    public function joinCourse(Request $request)
    {
        $this->validate($request, [
            'course' => 'required|string'
        ]);
        $course = Course::where('uuid', $request['course'])->first();
        if (!$course) {
            abort(400, 'Course not found');
        }

        $user = Auth::user();
        Auth::user()->student->courses()->syncWithoutDetaching($course->id);

        NotificationService::joinCourseNotification($course);

        ActivityJob::dispatchAfterResponse('Course registration successful', "Your have successfully registered for {$course->title}", Auth::id());
        ActivityJob::dispatchAfterResponse('New Student Registration', "{$user->firstname} {$user->lastname} has successfully registered for your course:{$course->title}", $course->teacher->id);

        return response()->json(['message' => 'Course added successfully']);
    }

    public function getSchedules()
    {
        $courses = Auth::user()->student->courses;
        $schedule = [];
        foreach ($courses as $course) {
            foreach ($course->schedules as $val)
                array_push($schedule, $val);
        }
        return response()->json($schedule, 200);
    }

    public function upcomingSchedules()
    {
        $courses = Auth::user()->student->courses;
        $schedule = [];
        foreach ($courses as $course) {
            foreach ($course->schedules as $val) {
                if ($val->end > now()) {
                    array_push($schedule, $val);
                }
            }
            // array_push($schedule, $val);
        }
        return response()->json($schedule, 200);
    }
}
