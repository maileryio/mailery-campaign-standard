<?php

namespace Mailery\Campaign\Regular\Service;

use Cycle\ORM\ORMInterface;
use Cycle\ORM\Transaction;
use Mailery\Campaign\Regular\Entity\RegularCampaign;
use Mailery\Campaign\Regular\ValueObject\CampaignValueObject;

class CampaignCrudService
{
    /**
     * @var ORMInterface
     */
    private ORMInterface $orm;

    /**
     * @param ORMInterface $orm
     */
    public function __construct(ORMInterface $orm)
    {
        $this->orm = $orm;
    }

    /**
     * @param CampaignValueObject $valueObject
     * @return RegularCampaign
     */
    public function create(CampaignValueObject $valueObject): RegularCampaign
    {
        $campaign = (new RegularCampaign())
            ->setName($valueObject->getName())
            ->setBrand($valueObject->getBrand())
        ;

        $tr = new Transaction($this->orm);
        $tr->persist($campaign);
        $tr->run();

        return $campaign;
    }

    /**
     * @param RegularCampaign $campaign
     * @param CampaignValueObject $valueObject
     * @return Campaign
     */
    public function update(RegularCampaign $campaign, CampaignValueObject $valueObject): RegularCampaign
    {
        $campaign = $campaign
            ->setName($valueObject->getName())
            ->setBrand($valueObject->getBrand())
        ;

        $tr = new Transaction($this->orm);
        $tr->persist($campaign);
        $tr->run();

        return $campaign;
    }

    /**
     * @param RegularCampaign $campaign
     * @param CampaignValueObject $valueObject
     * @return Campaign
     */
    public function updateContent(RegularCampaign $campaign, CampaignValueObject $valueObject): RegularCampaign
    {
        $campaign = $campaign
            ->setContent($valueObject->getContent())
        ;

        $tr = new Transaction($this->orm);
        $tr->persist($campaign);
        $tr->run();

        return $campaign;
    }

    /**
     * @param RegularCampaign $campaign
     * @return bool
     */
    public function delete(RegularCampaign $campaign): bool
    {
        $tr = new Transaction($this->orm);
        $tr->delete($campaign);
        $tr->run();

        return true;
    }
}
