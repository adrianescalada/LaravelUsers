<?php

namespace wdna\ApiGuard\Models\Mixins;

use wdna\ApiGuard\Models\ApiKey;

trait Apikeyable
{
    public function apiKeys()
    {
        return $this->morphMany(config('apiguard.models.api_key', ApiKey::class), 'apikeyable');
    }

    public function createApiKey()
    {
        return ApiKey::make($this);
    }
}
