<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Metric extends Model
{
    use HasFactory;

    protected $table = 'metrics';

    protected $fillable = [
        'partner_id',
        'step_one',
        'step_two',
        'step_three',
        'step_four',
    ];
}
