<?php

namespace Mailery\Campaign\Standard\Service;

use Cycle\ORM\ORMInterface;
use Mailery\Campaign\Standard\Entity\StandardCampaign;
use Mailery\Campaign\Standard\ValueObject\CampaignTrackingValueObject;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

class CampaignTrackingCrudService
{
    /**
     * @param ORMInterface $orm
     */
    public function __construct(
        private ORMInterface $orm
    ) {}


    /**
     * @param StandardCampaign $campaign
     * @param CampaignTrackingValueObject $valueObject
     * @return Campaign
     */
    public function update(StandardCampaign $campaign, CampaignTrackingValueObject $valueObject): StandardCampaign
    {
        $campaign = $campaign
            ->setTrackClicks($valueObject->getTrackClicks())
            ->setTrackOpens($valueObject->getTrackOpens())
            ->setEnableUtmTags($valueObject->getEnableUtmTags())
            ->setUtmTags($valueObject->getUtmTags())
        ;

        (new EntityWriter($this->orm))->write([$campaign]);

        return $campaign;
    }
}
