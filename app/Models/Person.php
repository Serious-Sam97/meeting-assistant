<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'people';

    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }
}
