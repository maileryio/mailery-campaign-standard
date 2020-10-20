<?php

use Mailery\Campaign\Provider\CampaignTypeConfigs;

return [
    CampaignTypeConfigs::class => static function () use ($params) {
        $configs = $params['maileryio/mailery-campaign-regular']['types'] ?? [];
        return new CampaignTypeConfigs($configs);
    },
];
