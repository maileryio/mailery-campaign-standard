<?php

namespace Mailery\Campaign\Standard\Model;

use Mailery\Campaign\Model\CampaignTypeInterface;

class StandardCampaignType implements CampaignTypeInterface
{
    /**
     * @inheritdoc
     */
    public function getLabel(): string
    {
        return 'Standard campaign';
    }

    /**
     * @inheritdoc
     */
    public function getCreateLabel(): string
    {
        return 'Standard campaign';
    }

    /**
     * @inheritdoc
     */
    public function getCreateRouteName(): ?string
    {
        return '/campaign/standard/create';
    }

    /**
     * @inheritdoc
     */
    public function getCreateRouteParams(): array
    {
        return [];
    }
}
