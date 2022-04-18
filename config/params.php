<?php

declare(strict_types=1);

use Yiisoft\Definitions\Reference;
use Mailery\Campaign\Standard\Model\StandardCampaignType;
use Mailery\Campaign\Standard\Entity\StandardCampaign;

return [
    'maileryio/mailery-campaign' => [
        'types' => [
            Reference::to(StandardCampaignType::class),
        ],
    ],

    'maileryio/mailery-activity-log' => [
        'entity-groups' => [
            'campaign' => [
                'entities' => [
                    StandardCampaign::class,
                ],
            ],
        ],
    ],

    'yiisoft/yii-cycle' => [
        'entity-paths' => [
            '@vendor/maileryio/mailery-campaign-standard/src/Entity',
        ],
    ],

    'maileryio/mailery-menu-sidebar' => [
        'items' => [
            'campaigns' => [
                'items' => [
                    'campaigns' => [
                        'activeRouteNames' => [
                            '/campaign/standard/view',
                            '/campaign/standard/create',
                            '/campaign/standard/edit',
                            '/campaign/standard/sendout',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
