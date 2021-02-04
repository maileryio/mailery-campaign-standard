<?php

namespace Mailery\Campaign\Standard\Service;

use Cycle\ORM\ORMInterface;
use Cycle\ORM\Transaction;
use Mailery\Campaign\Standard\Entity\StandardCampaign;
use Mailery\Campaign\Standard\ValueObject\CampaignValueObject;

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
     * @return StandardCampaign
     */
    public function create(CampaignValueObject $valueObject): StandardCampaign
    {
        $campaign = (new StandardCampaign())
            ->setName($valueObject->getName())
            ->setBrand($valueObject->getBrand())
            ->setTemplate($valueObject->getTemplate())
        ;

        foreach ($valueObject->getGroups() as $group) {
            $campaign->getGroups()->add($group);
        }

        $tr = new Transaction($this->orm);
        $tr->persist($campaign);
        $tr->run();

        return $campaign;
    }

    /**
     * @param StandardCampaign $campaign
     * @param CampaignValueObject $valueObject
     * @return Campaign
     */
    public function update(StandardCampaign $campaign, CampaignValueObject $valueObject): StandardCampaign
    {
        $campaign = $campaign
            ->setName($valueObject->getName())
            ->setBrand($valueObject->getBrand())
            ->setTemplate($valueObject->getTemplate())
        ;

        foreach ($campaign->getGroups() as $group) {
            $campaign->getGroups()->removeElement($group);
        }

        foreach ($valueObject->getGroups() as $group) {
            $campaign->getGroups()->add($group);
        }

        $tr = new Transaction($this->orm);
        $tr->persist($campaign);
        $tr->run();

        return $campaign;
    }

    /**
     * @param StandardCampaign $campaign
     * @return bool
     */
    public function delete(StandardCampaign $campaign): bool
    {
        foreach ($campaign->getGroups() as $groupPivot) {
             $campaign->getGroups()->removeElement($groupPivot);
        }

        $tr = new Transaction($this->orm);
        $tr->delete($campaign);
        $tr->run();

        return true;
    }
}
