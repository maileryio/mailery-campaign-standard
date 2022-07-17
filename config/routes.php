<?php

declare(strict_types=1);

use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Mailery\Campaign\Middleware\AssetBundleMiddleware;
use Mailery\Campaign\Standard\Controller\DefaultController;

return [
    Group::create('/brand/{brandId:\d+}')
        ->routes(
            Group::create('/campaign/standard')
                ->middleware(AssetBundleMiddleware::class)
                ->routes(
                    Route::get('/view/{id:\d+}')
                        ->name('/campaign/standard/view')
                        ->action([DefaultController::class, 'view']),
                    Route::get('/preview/{id:\d+}')
                        ->name('/campaign/standard/preview')
                        ->action([DefaultController::class, 'preview']),
                    Route::methods(['GET', 'POST'], '/create')
                        ->name('/campaign/standard/create')
                        ->action([DefaultController::class, 'create']),
                    Route::methods(['GET', 'POST'], '/edit/{id:\d+}')
                        ->name('/campaign/standard/edit')
                        ->action([DefaultController::class, 'edit']),
                    Route::methods(['GET', 'POST'], '/content/{id:\d+}')
                        ->name('/campaign/standard/content')
                        ->action([DefaultController::class, 'content']),
                    Route::methods(['GET', 'POST'], '/recipients/{id:\d+}')
                        ->name('/campaign/standard/recipients')
                        ->action([DefaultController::class, 'recipients']),
                    Route::methods(['GET', 'POST'], '/tracking/{id:\d+}')
                        ->name('/campaign/standard/tracking')
                        ->action([DefaultController::class, 'tracking']),
                    Route::methods(['GET', 'POST'], '/schedule/{id:\d+}')
                        ->name('/campaign/standard/schedule')
                        ->action([DefaultController::class, 'schedule']),
                    Route::methods(['GET', 'POST'], '/report/{id:\d+}')
                        ->name('/campaign/standard/report')
                        ->action([DefaultController::class, 'report']),
                    Route::delete('/schedule/{id:\d+}/cancel')
                        ->name('/campaign/standard/schedule/cancel')
                        ->action([DefaultController::class, 'scheduleCancel']),
                    Route::delete('/delete/{id:\d+}')
                        ->name('/campaign/standard/delete')
                        ->action([DefaultController::class, 'delete']),
                )
        )
];
