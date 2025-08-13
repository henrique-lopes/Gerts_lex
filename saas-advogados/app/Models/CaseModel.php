<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class CaseModel extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $table = 'cases'; // Explicitly define table name

    protected $fillable = [
        'tenant_id',
        'client_id',
        'lawyer_id',
        'case_number',
        'case_type',
        'court',
        'status',
        'description',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function lawyer()
    {
        return $this->belongsTo(Lawyer::class);
    }

    public function fees()
    {
        return $this->hasMany(Fee::class, 'case_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'case_id');
    }

    public function deadlines()
    {
        return $this->hasMany(Deadline::class, 'case_id');
    }

    public function documents()
    {
        return $this->hasMany(CaseDocument::class, 'case_id');
    }
}