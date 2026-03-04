<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;
    use BelongsToTenant;

    protected $fillable = [
        'subscriber_id',
        'user_id',
        'name',
        'code',
        'billing_address',
        'status',
    ];

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function clientContacts(): HasMany
    {
        return $this->hasMany(ClientContact::class);
    }

    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }

    public function siteAllowanceConfigs(): HasMany
    {
        return $this->hasMany(SiteAllowanceConfig::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
