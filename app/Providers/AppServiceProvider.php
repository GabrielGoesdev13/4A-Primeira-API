<?php

namespace App\Providers;

use App\Repositories\EloquentProdutoRepository;
use App\Repositories\ProdutoRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ProdutoRepositoryInterface::class, EloquentProdutoRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
