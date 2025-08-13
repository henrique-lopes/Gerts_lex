<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class CaseDocument extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $fillable = [
        'case_id',
        'name',
        'file_path',
        'type',
        'uploaded_by',
    ];

    public function case()
    {
        return $this->belongsTo(CaseModel::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}