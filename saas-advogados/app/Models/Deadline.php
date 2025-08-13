<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Deadline extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $fillable = [
        'case_id',
        'title',
        'description',
        'due_date',
        'status',
        'priority',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function case()
    {
        return $this->belongsTo(CaseModel::class);
    }
}