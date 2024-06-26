<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $hidden = ['id'];

    protected $fillable = ['bio'];
    public function courses(){
        return $this->hasMany(Course::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
