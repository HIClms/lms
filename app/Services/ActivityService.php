<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Activity;

class ActivityService
{
    public static function saveActivity($subject, $body, $user_id)
    {
        $activity = new Activity();
        $activity->subject = $subject;
        $activity->body = $body;
        $activity->user_id = $user_id;
        $activity->save();
        return true;
    }

    public static function scheduleCreatedActivity($course, $schedule)
    {
        $subject = "Schedule created for {$course->title} - {$schedule->title}";
        foreach ($course->students as $stu) {
            ActivityService::saveActivity($subject, "A class has been scheduled for course:{$course->title}, titled - $schedule->title", $stu->user_id);
        }
        ActivityService::saveActivity($subject, "A class has been scheduled for course: {$course->title}, titled - $schedule->title", $course->teacher->id);
        return true;
    }

    public static function readSingleActivity($activityId)
    {
        DB::table('activities')->where('id', $activityId)->update(['read' => 1]);
        return true;
    }

    public static function readAllActivity($user_id)
    {
        DB::table('activities')->where('user_id', $user_id)->where('read', 0)->update(['read' => 1]);
        return true;
    }
}
