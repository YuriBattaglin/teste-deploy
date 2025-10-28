<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Traffic extends Model
{
    use HasFactory;

    protected $table = 'traffic';

    protected $fillable = [
        'partner_id',
        'impressions',
        'clicks',
        'ad_investment',
        'leads',
        'observation',
        'start_date',
        'end_date',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }
}
