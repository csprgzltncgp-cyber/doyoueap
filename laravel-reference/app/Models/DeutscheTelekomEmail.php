<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeutscheTelekomEmail extends Model
{
    protected $fillable = [
        'email',
        'case_id_1',
        'case_id_2',
        'case_id_3',
    ];
}
