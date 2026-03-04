<?php

namespace App\Support\Tenancy;

class TenantManager
{
    protected ?int $tenantId = null;

    public function setId(?int $tenantId): void
    {
        $this->tenantId = $tenantId;
    }

    public function id(): ?int
    {
        return $this->tenantId;
    }

    public function isScoped(): bool
    {
        return $this->tenantId !== null;
    }
}
