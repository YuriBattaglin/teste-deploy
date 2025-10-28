<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;

    protected $table = 'partners';

    protected $fillable = [
        'logo',
        'description',
        'cpf_cnpj',
        'email',
        'phone',
        'status',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'partner_id');
    }

     public function squad()
    {
        return $this->belongsTo(Squad::class, 'squad_id');
    }

      public function metrics()
    {
        return $this->hasOne(Metric::class, 'partner_id');
    }
}
