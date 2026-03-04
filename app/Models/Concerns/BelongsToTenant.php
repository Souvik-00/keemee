<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::creating(function ($model): void {
            if ($model->subscriber_id) {
                return;
            }

            $tenantId = tenant_id();

            if (! $tenantId && auth()->check() && ! auth()->user()->hasRole('leadership')) {
                $tenantId = (int) auth()->user()->subscriber_id;
            }

            if ($tenantId) {
                $model->subscriber_id = $tenantId;
            }
        });

        static::addGlobalScope('tenant', function (Builder $query): void {
            $tenantId = tenant_id();

            if (! $tenantId && auth()->check() && ! auth()->user()->hasRole('leadership')) {
                $tenantId = (int) auth()->user()->subscriber_id;
            }

            if ($tenantId) {
                $query->where($query->qualifyColumn('subscriber_id'), $tenantId);
            }
        });
    }
}
