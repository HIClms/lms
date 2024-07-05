<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Activity extends Model
{
    use HasFactory;

    protected $appends = ['diff'];

    public function getDiffAttribute()
    {
        return Carbon::create($this->created_at)->diffForHumans(now());
    }
}
