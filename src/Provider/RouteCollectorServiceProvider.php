<?php

namespace Mailery\Campaign\Regular\Provider;

use Yiisoft\Di\Container;
use Yiisoft\Di\Support\ServiceProvider;
use Yiisoft\Router\RouteCollectorInterface;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Mailery\Campaign\Regular\Controller\DefaultController;

final class RouteCollectorServiceProvider extends ServiceProvider
{
    public function register(Container $container): void
    {
        /** @var RouteCollectorInterface $collector */
        $collector = $container->get(RouteCollectorInterface::class);

        $collector->addGroup(
            Group::create(
                '/brand/{brandId:\d+}',
                [
                    Route::get('/campaign/regular/view/{id:\d+}', [DefaultController::class, 'view'])
                        ->name('/campaign/regular/view'),
                    Route::methods(['GET', 'POST'], '/campaign/regular/create', [DefaultController::class, 'create'])
                        ->name('/campaign/regular/create'),
                    Route::methods(['GET', 'POST'], '/campaign/regular/edit/{id:\d+}', [DefaultController::class, 'edit'])
                        ->name('/campaign/regular/edit'),
                ]
            )
        );
    }
}
