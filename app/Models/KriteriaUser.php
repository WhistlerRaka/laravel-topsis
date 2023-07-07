<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KriteriaUser extends Model
{
    use HasFactory;
    protected $fillable = [
        'kriteria_id',
        'bobot_kriteria',
        'user_id',
    ];
}
