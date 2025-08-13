<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Appointment extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $fillable = [
        'tenant_id',
        'lawyer_id',
        'case_id',
        'title',
        'description',
        'start_time',
        'end_time',
        'type',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function lawyer()
    {
        return $this->belongsTo(Lawyer::class);
    }

    public function case()
    {
        return $this->belongsTo(CaseModel::class);
    }
}