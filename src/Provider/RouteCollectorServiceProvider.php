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
            Group::create('/brand/{brandId:\d+}')
                ->routes(
                    Route::get('/campaign/standard/view/{id:\d+}')
                        ->name('/campaign/standard/view')
                        ->action([DefaultController::class, 'view']),
                    Route::methods(['GET', 'POST'], '/campaign/standard/create')
                        ->name('/campaign/standard/create')
                        ->action([DefaultController::class, 'create']),
                    Route::methods(['GET', 'POST'], '/campaign/standard/edit/{id:\d+}')
                        ->name('/campaign/standard/edit')
                        ->action([DefaultController::class, 'edit']),
                    Route::methods(['POST'], '/campaign/standard/test/{id:\d+}')
                        ->name('/campaign/standard/test')
                        ->action([DefaultController::class, 'test']),
                )
        );
    }
}
