<?php

namespace App\Models;

use App\Models\HomeRoom;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Teacher extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function classroom()
    {
        return $this->hasMany(HomeRoom::class, 'teachers_id', 'id');
    }
}