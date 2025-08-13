<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Fee extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $fillable = [
        'case_id',
        'amount',
        'type',
        'status',
        'due_date',
        'paid_date',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    public function case()
    {
        return $this->belongsTo(CaseModel::class);
    }
}