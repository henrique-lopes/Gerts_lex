<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Client extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'phone',
        'cpf_cnpj',
        'address',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function cases()
    {
        return $this->hasMany(CaseModel::class, 'client_id');
    }
}