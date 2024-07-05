<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Student;
use App\Models\Chat;

class AdminController extends Controller
{
    public function kpi()
    {
        $courses = DB::table('courses')->count();
        $students = DB::table('students')->count();
        $teachers = DB::table('teachers')->count();

        $data = [
            'courses' => $courses,
            'students' => $students,
            'teachers' => $teachers
        ];

        return $data;
    }

    public function students()
    {
        $students = User::where('role_id', 1)->get();
        return $students;
    }

    public function teachers()
    {
        $teachers = User::where('role_id', 2)->get();
        return $teachers;
    }

    public function chats()
    {
        $users = User::with('chats')->paginate(15);
        return $users;
    }

    public function saveChat(Request $request)
    {
        $this->validate($request, [
            'user' => 'required|string',
            'message' => 'required|string',
        ]);

        $user = DB::table('users')->where('uuid', $request->user)->first();
        if (!$user) {
            abort(400, 'User not found');
        }

        $chat = new Chat();
        $chat->sender_id = 0;
        $chat->user_id = $user->id;
        $chat->message = $request->message;
        $chat->save();

        return response()->json(['message' => 'Message sent', 'data' => $chat]);
    }

    public function sendMessageToAllStudents(Request $request)
    {
        $this->validate($request, [
            'message' => 'required|string',
        ]);
        $users = DB::table('students')->select('user_id')->get();
        foreach ($users as $user) {
            DB::table('chats')->insert([
                'sender_id' => 0,
                'user_id' => $user->user_id,
                'message' => $request->message,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        return response()->json(['message' => 'Message sent']);
    }

    public function sendMessageToAllTeachers(Request $request)
    {
        $this->validate($request, [
            'message' => 'required|string',
        ]);
        $users = DB::table('teachers')->select('user_id')->get();
        foreach ($users as $user) {
            DB::table('chats')->insert([
                'sender_id' => 0,
                'user_id' => $user->user_id,
                'message' => $request->message,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        return response()->json(['message' => 'Message sent']);
    }
}
