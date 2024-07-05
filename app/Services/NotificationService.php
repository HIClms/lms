<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Log;
use App\Jobs\SendMailJob;
use Carbon\Carbon;

class NotificationService
{
    public static function joinCourseNotification($course)
    {
        $user = Auth::user();
        $subject = "{$course->title} Course Registration Successful";
        $message = "
            <p>Dear {$user->firstname},</p>
            <p style='margin-bottom: 5px'>Your enrollment to {$course->title} was successful, </p>
            <p style='margin-bottom: 8px'>All the best! </p>
            <p><b>Best Regards</b></p>
            <p><b>HighImapact</b></p>
        ";

        $subject2 = "{$user->firstname} {$user->lastname} Registered for {$course->title}";
        $message2 = "
            <p>Dear {$course->teacher->firstname},</p>
            <p style='margin-bottom: 8px'>{$user->firstname} {$user->lastname} has successfully enrolled for your course {$course->title} </p>
            <p style='margin-bottom: 8px'>All the best! </p>
            <p><b>Best Regards</b></p>
            <p><b>HighImapact</b></p>
        ";

        SendMailJob::dispatchAfterResponse($user->email, $subject, $message);
        SendMailJob::dispatchAfterResponse($course->teacher->email, $subject2, $message2);

        return true;
    }

    public static function scheduleCreatedNotification($course, $schedule)
    {

        $subject = "Schedule created for {$course->title} - {$schedule->title}";
        foreach ($course->students as $stu) {
            $sd = Carbon::parse($schedule->start)->format('M,d Y h:i:s');
            $ed = Carbon::parse($schedule->end)->format('M,d Y h:i:s');
            $message = "
                <h5>{$schedule->title}</h5>
                <p>Dear {$stu->user->firstname},</p>
                <p>A schedule has been created for {$course->title}, the details are below: </p>
                <p>Title: {$schedule->title} </p>
                <p>Description: {$schedule->content} </p>
                <p>Start: {$sd}</p>
                <p>End: {$ed}</p>
                <p>Link: {$schedule->url}</p>
                <p><b>Best Regards</b></p>
                <p><b>HighImapact</b></p>
            ";
            SendMailJob::dispatchAfterResponse($stu->user->email, $subject, $message);
        }
    }
}
