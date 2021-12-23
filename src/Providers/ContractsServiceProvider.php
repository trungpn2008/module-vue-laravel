<?php

namespace DXMB\Modules\Providers;

use Illuminate\Support\ServiceProvider;
use DXMB\Modules\Contracts\RepositoryInterface;
use DXMB\Modules\Laravel\LaravelFileRepository;

class ContractsServiceProvider extends ServiceProvider
{
    /**
     * Register some binding.
     */
    public function register()
    {
        $this->app->bind(RepositoryInterface::class, LaravelFileRepository::class);
    }
}
