<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        "tenant_id",
        "type",
        "stripe_id",
        "stripe_status",
        "stripe_plan",
        "quantity",
        "trial_ends_at",
        "ends_at",
    ];

    protected $casts = [
        "trial_ends_at" => "datetime",
        "ends_at" => "datetime",
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
