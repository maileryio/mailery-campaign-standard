<?php

namespace Mailery\Campaign\Standard\Provider;

use Psr\Container\ContainerInterface;
use Yiisoft\Di\Support\ServiceProvider;
use Yiisoft\Router\RouteCollectorInterface;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Mailery\Campaign\Standard\Controller\DefaultController;

final class RouteCollectorServiceProvider extends ServiceProvider
{
    /**
     * @param ContainerInterface $container
     * @return void
     */
    public function register(ContainerInterface $container): void
    {
        /** @var RouteCollectorInterface $collector */
        $collector = $container->get(RouteCollectorInterface::class);

        $collector->addGroup(
            Group::create(
                '/brand/{brandId:\d+}',
                [
                    Route::get('/campaign/standard/view/{id:\d+}', [DefaultController::class, 'view'])
                        ->name('/campaign/standard/view'),
                    Route::methods(['GET', 'POST'], '/campaign/standard/create', [DefaultController::class, 'create'])
                        ->name('/campaign/standard/create'),
                    Route::methods(['GET', 'POST'], '/campaign/standard/edit/{id:\d+}', [DefaultController::class, 'edit'])
                        ->name('/campaign/standard/edit'),
                ]
            )
        );
    }
}
