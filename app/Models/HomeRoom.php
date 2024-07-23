<?php

namespace App\Models;

use App\Models\Periode;
use App\Models\Teacher;
use App\Models\Classroom;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HomeRoom extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teachers_id', 'id');
    }
    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'classrooms_id', 'id');
    }
    public function periode()
    {
        return $this->belongsTo(Periode::class, 'periode_id', 'id');
    }
}