<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyRegister extends Model
{
    use HasFactory;

    protected $table = 'daily_registers';

    protected $fillable = [
        'partner_id',
        'user_id',
        'date',
        'step_one',
        'step_two',
        'step_three',
        'step_four',
        'notes',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

        public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
