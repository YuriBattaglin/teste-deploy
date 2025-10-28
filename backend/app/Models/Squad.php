<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Squad extends Model
{
    use HasFactory;

    protected $table = 'squads';

    protected $fillable = [
        'name',
        'leader_id',
        'active',
        'description',
        'members'
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function membersList()
    {
        return User::whereIn('id', $this->members ?? [])->get();
    }

    public function partners()
    {
        return $this->hasMany(Partner::class, 'squad_id');
    }

    public function getMembersAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function setMembersAttribute($value)
    {
        $this->attributes['members'] = json_encode($value);
    }
}
