<?php

namespace Mailery\Campaign\Standard\Model;

use Mailery\Campaign\Model\CampaignTypeInterface;
use Mailery\Campaign\Standard\Entity\StandardCampaign;

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

    /**
     * @param object $entity
     * @return bool
     */
    public function isEntitySameType(object $entity): bool
    {
        return $entity instanceof StandardCampaign;
    }
}
