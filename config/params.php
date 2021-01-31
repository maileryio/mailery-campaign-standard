<?php

declare(strict_types=1);

use Mailery\Campaign\Regular\Model\RegularCampaignType;

return [
    'maileryio/mailery-campaign' => [
        'types' => [
            RegularCampaignType::class => RegularCampaignType::class,
        ],
    ],

    'yiisoft/yii-cycle' => [
        'annotated-entity-paths' => [
            '@vendor/maileryio/mailery-campaign-regular/src/Entity',
        ],
    ],

    'maileryio/mailery-menu-sidebar' => [
        'items' => [
            'campaigns' => [
                'items' => [
                    'campaigns' => [
                        'activeRouteNames' => [
                            '/campaign/regular/view',
                            '/campaign/regular/create',
                            '/campaign/regular/edit',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
