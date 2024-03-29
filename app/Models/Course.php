<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory;

    protected $appends = ['modules', 'students_count', 'teacher', 'check_student'];

    protected $hidden = [
        'teacher_id'
    ];

    public static function boot(): void
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = Str::uuid()->toString();
        });
    }

    public function getModulesAttribute(){
        return $this->modules()->get();
    }

    public function getTeacherAttribute(){
        return $this->teacher()->first()->user;
    }

    public function getStudentsCountAttribute(){
        return $this->students()->count();
    }

    public function getCheckStudentAttribute(){
        $val = $this->students->where('user_id', Auth::user()->id)->first();
        if(!$val){
            return false;
        }
        return true;
    }

    public function modules(){
        return $this->hasMany(Module::class);
    }
    public function students(){
        return $this->belongsToMany(Student::class, 'course_student')->withTimestamps();
    }

    public function teacher(){
        return $this->belongsTo(Teacher::class);
    }

    public function schedules(){
        return $this->hasMany(Schedule::class);
    }
}
