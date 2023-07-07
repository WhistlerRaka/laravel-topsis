<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailKriteria extends Model
{
    use HasFactory;

    protected $fillable = [
        'kriteria_id',
        'poin',
        'keterangan',
        'poin_optional',
        'data_optional'
    ];

}
