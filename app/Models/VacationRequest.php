<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VacationRequest extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'status', 'team', 'role', 'roles', 'days'];
}

// public function getDaysAttribute($value)
// {
//     return json_decode($value, true); // Converte o JSON de volta para um array
// }


