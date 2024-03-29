<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Module extends Model
{
    use HasFactory;

    protected $appends = ['videos', 'documents'];

    public static function boot(): void
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = Str::uuid()->toString();
        });
    }

    public function getVideosAttribute(){
        return $this->videos()->get();
    }

    public function getDocumentsAttribute(){
        return $this->documents()->get();
    }

    public function videos(){
        return $this->hasMany(Video::class);
    }

    public function documents(){
        return $this->hasMany(Document::class);
    }
}
