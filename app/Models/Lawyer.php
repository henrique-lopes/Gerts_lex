<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lawyer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'oab_number',
        'oab_state',
        'specialties',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cases()
    {
        return $this->hasMany(CaseModel::class, 'lawyer_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'lawyer_id');
    }
}