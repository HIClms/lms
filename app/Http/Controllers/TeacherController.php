<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Document;
use App\Models\Module;
use App\Models\Schedule;
use App\Models\Video;
use Carbon\Carbon;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpParser\Node\Expr\AssignOp\Mod;
use App\Jobs\ActivityJob;
use App\Services\ActivityService;
use App\Services\NotificationService;

class TeacherController extends Controller
{
    public function getProfile()
    {
        return response()->json(Auth::user()->load('teacher'), 200);
    }

    public function getKpi()
    {
    }

    public function getCourse($id)
    {
        $course = Course::where('uuid', $id)->first();
        if (!$course) {
            abort(400, 'Course not found');
        }

        if ($course->teacher_id != Auth::user()->teacher->id) {
            abort(400, 'Unauthorised access to course, please login again');
        }

        return $course;
    }

    public function updateCourse(Request $request)
    {
        $this->validate($request, [
            'uuid' => 'required|string',
            'title' => 'required|string',
            'slack_url' => 'required|url',
            'description' => 'required|string'
        ]);

        $course = Course::where('uuid', $request->uuid)->first();
        if (!$course) {
            abort(400, 'Course not found');
        }
        if ($course->teacher_id != Auth::user()->teacher->id) {
            abort(400, 'Unauthorized action, please login');
        }
        $course->title = $request->title;
        $course->description = $request->description;
        $course->slack_url = $request->slack_url;
        $course->save();
        return response(['message' => 'Course updated']);
    }

    public function updateModule(Request $request)
    {
        $this->validate($request, [
            'uuid' => 'required|string',
            'title' => 'required|string',
        ]);

        $module = Module::where('uuid', $request->uuid)->first();
        if (!$module) {
            abort(400, 'Module not found');
        }

        $module->title = $request->title;
        $module->save();

        return response(['message' => 'Module updated']);
    }

    public function addModule(Request $request)
    {
        $this->validate($request, [
            'uuid' => 'required|string',
            'title' => 'required|string',
            'videos.*' => 'required',
            'documents.*' => 'nullable'
        ]);
        $course = Course::where('uuid', $request['uuid'])->first();
        if (!$course) {
            abort(400, 'Course not found');
        }
        try {
            DB::beginTransaction();
            $mod = new Module();
            $mod->title = $request['title'];
            $mod->course_id = $course->id;
            $mod->save();

            if ($request['videos']) {
                foreach ($request['videos'] as $vids) {
                    $img = cloudinary()->uploadFile($vids->getRealPath())->getSecurePath();
                    $vd = new Video();
                    $vd->module_id = $mod->id;
                    //upload profile image
                    $vd->url = $img;
                    $vd->save();
                }
            }

            if ($request['documents']) {
                foreach ($request['documents'] as $file) {
                    $img = cloudinary()->uploadFile($file->getRealPath())->getSecurePath();
                    $doc = new Document();
                    $doc->module_id = $mod->id;
                    //upload profile image
                    $doc->url = $img;
                    $doc->save();
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort(400, $e->getMessage());
        }
        return response(['message' => 'Module added successfully']);
    }

    public function addVideo(Request $request)
    {
        $this->validate($request, [
            'uuid' => 'required|string',
            'videos.*' => 'required',
        ], ['uuid.required' => 'Invalid module']);

        $mod = Module::where('uuid', $request['uuid'])->first();
        if (!$mod) {
            abort(400, 'Module not found');
        }

        try {
            DB::beginTransaction();
            if ($request['videos']) {
                foreach ($request['videos'] as $vids) {
                    $img = cloudinary()->uploadFile($vids->getRealPath())->getSecurePath();
                    $vd = new Video();
                    $vd->module_id = $mod->id;
                    $vd->url = $img;
                    $vd->save();
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort(400, $e->getMessage());
        }
        return response(['message' => 'Video added successfully']);
    }

    public function addDocument(Request $request)
    {
        $this->validate($request, [
            'uuid' => 'required|string',
            'documents.*' => 'required',
        ], ['uuid.required' => 'Invalid module', 'document.required' => 'Document file is required']);

        $mod = Module::where('uuid', $request['uuid'])->first();
        if (!$mod) {
            abort(400, 'Module not found');
        }

        try {
            DB::beginTransaction();
            if ($request['documents']) {
                foreach ($request['documents'] as $file) {
                    $img = cloudinary()->uploadFile($file->getRealPath())->getSecurePath();
                    $doc = new Document();
                    $doc->module_id = $mod->id;
                    //upload profile image
                    $doc->url = $img;
                    $doc->save();
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort(400, $e->getMessage());
        }
        return response(['message' => 'Video added successfully']);
    }
    public function createCourse(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string',
            'slack_url' => 'required|string',
            'course_image' => 'required|string',
            'description' => 'required|string',
            'module.*.title' => 'required|string',
            'module.*.videos.*' => 'required|string',
            'module.*.documents.*' => 'required|string'
        ]);

        // return $request['module'] ;
        // return $request['module'][0]['videos'];
        // DB::transaction(function() use ($request) {
        // $uploadedFileUrl = cloudinary()->uploadFile($request->file('course_image')->getRealPath())->getSecurePath();
        // return ['id'=>$uploadedFileUrl->getPublicId(), 'data'=>$uploadedFileUrl->getSecurePath()];
        try {
            DB::beginTransaction();
            $course = new Course();
            $course->teacher_id = Auth::user()->teacher->id;
            $course->title = $request['title'];
            $course->description = $request['description'];
            $course->slack_url = $request['slack_url'];
            //upload profile image
            $course->image = $request['course_image'];
            $course->save();


            foreach ($request['module'] as $key => $value) {
                $mod = new Module();
                $mod->title = $value['title'];
                $mod->course_id = $course->id;
                $mod->save();

                if (array_key_exists('videos', $value)) {
                    foreach ($value['videos'] as $vids) {
                        // $img = cloudinary()->uploadFile($request->file('course_image')->getRealPath())->getSecurePath();
                        $vd = new Video();
                        $vd->module_id = $mod->id;
                        //upload profile image
                        $vd->url = $vids;
                        $vd->save();
                    }
                }
                if (array_key_exists('documents', $value)) {
                    foreach ($value['documents'] as $file) {
                        // $img = cloudinary()->uploadFile($request->file('course_image')->getRealPath())->getSecurePath();
                        $doc = new Document();
                        $doc->module_id = $mod->id;
                        //upload profile image
                        $doc->url = $file;
                        $doc->save();
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort(400, $e->getMessage());
        }
        // });
        ActivityJob::dispatchAfterResponse("New Course Created - {$request->title}", "You have successfully created a new course titled {$request->title}", Auth::id());

        return response(['message' => 'Course created successfully']);
    }

    public function deleteVideo(Request $request)
    {
        $this->validate($request, [
            'uuid' => 'required',
        ]);
        Video::where('uuid', $request['uuid'])->delete();
        return response()->json('Video deleted successfully', 200);
    }

    public function deleteDocument(Request $request)
    {
        $this->validate($request, [
            'uuid' => 'required',
        ]);
        Document::where('uuid', $request['uuid'])->delete();
        return response()->json('Document deleted successfully', 200);
    }

    public function deleteModule(Request $request)
    {
        $this->validate($request, [
            'uuid' => 'required',
        ]);
        $module = Module::where('uuid', $request['uuid'])->first();
        if (!$module) {
            abort(400, 'Module not found');
        }
        Video::where('module_id', $module->id)->delete();
        $module->documents()->delete();
        $module->delete();

        return response()->json('Module deleted successfully', 200);
    }


    public function deleteCourse(Request $request)
    {
        $this->validate($request, [
            'uuid' => 'required',
        ]);
        $course = Course::where('uuid', $request['uuid'])->first();
        if (!$course) {
            abort(400, 'Course not found');
        }
        foreach ($course->modules as $module) {
            $module->videos()->delete();
            $module->documents()->delete();
        }
        $course->modules()->delete();
        return response()->json('Course deleted successfully', 200);
    }

    public function getCourses()
    {
        return Auth::user()->teacher->courses;
    }

    public function getStudents()
    {
        $courses = Auth::user()->teacher->courses;
        $students = [];
        foreach ($courses as $value) {
            $val['course'] = $value->title;
            foreach ($value->students as $student) {
                $val['student'] = $student->user;
                $val['enrolled_at'] = $student->pivot->created_at;
                array_push($students, $val);
            }
        }
        return response()->json($students, 200);
    }

    public function addSchedule(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string',
            'start_date' => 'required',
            'end_date' => 'required',
            'content' => 'nullable',
            'course' => 'required',
            'url' => 'required'
        ]);

        $course = Course::where('uuid', $request['course'])->first();
        if (!$course) {
            abort(400, 'Course not found');
        }

        $sche = new Schedule();
        $sche->title = $request['title'];
        $sche->start = $request['start_date'];
        $sche->end = $request['end_date'];
        $sche->teacher_id = Auth::user()->teacher->id;
        $sche->course_id = $course->id;
        $sche->content = $request['content'];
        $sche->url = $request['url'];
        $sche->save();


        //send email Notification to all students
        NotificationService::scheduleCreatedNotification($course, $sche);
        ActivityService::scheduleCreatedActivity($course, $sche);

        return response()->json('Schedule created successfully', 200);
    }

    public function getSchedules()
    {
        $sche = Schedule::where('teacher_id', Auth::user()->teacher->id)->where('end', '>', Carbon::now())->orderBy('start', 'asc')->get();
        return response()->json($sche, 200);
    }

    public function getReminders()
    {

        $sche = Schedule::where('teacher_id', Auth::user()->teacher->id)->orderBy('start', 'desc')->take(10);
        return response()->json($sche, 200);
    }
}
