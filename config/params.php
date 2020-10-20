<?php

declare(strict_types=1);

use Mailery\Campaign\Regular\Model\CampaignType;

return [
    'maileryio/mailery-campaign' => [
        'types' => [
            CampaignType::class => CampaignType::class,
        ],
    ],

    'yiisoft/yii-cycle' => [
        'annotated-entity-paths' => [
            '@vendor/maileryio/mailery-campaign-regular/src/Entity',
        ],
    ],
];
