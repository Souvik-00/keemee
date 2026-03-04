<?php

if (! function_exists('tenant_id')) {
    function tenant_id(): ?int
    {
        if (! app()->bound('tenant')) {
            return null;
        }

        return app('tenant')->id();
    }
}
