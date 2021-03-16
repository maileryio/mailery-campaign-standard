<?php

namespace Mailery\Campaign\Standard\Service;

use Cycle\ORM\ORMInterface;
use Mailery\Campaign\Standard\Entity\StandardCampaign;
use Mailery\Campaign\Standard\ValueObject\CampaignValueObject;
use Mailery\Brand\Entity\Brand;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

class CampaignCrudService
{
    /**
     * @var ORMInterface
     */
    private ORMInterface $orm;

    /**
     * @var Brand
     */
    private Brand $brand;

    /**
     * @param ORMInterface $orm
     */
    public function __construct(ORMInterface $orm)
    {
        $this->orm = $orm;
    }

    /**
     * @param Brand $brand
     * @return self
     */
    public function withBrand(Brand $brand): self
    {
        $new = clone $this;
        $new->brand = $brand;

        return $new;
    }

    /**
     * @param CampaignValueObject $valueObject
     * @return StandardCampaign
     */
    public function create(CampaignValueObject $valueObject): StandardCampaign
    {
        $campaign = (new StandardCampaign())
            ->setBrand($this->getBrand())
            ->setName($valueObject->getName())
            ->setTemplate($valueObject->getTemplate())
        ;

        foreach ($valueObject->getGroups() as $group) {
            $campaign->getGroups()->add($group);
        }

        (new EntityWriter($this->orm))->write([$campaign]);

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
            ->setBrand($this->getBrand())
            ->setName($valueObject->getName())
            ->setTemplate($valueObject->getTemplate())
        ;

        foreach ($campaign->getGroups() as $group) {
            $campaign->getGroups()->removeElement($group);
        }

        foreach ($valueObject->getGroups() as $group) {
            $campaign->getGroups()->add($group);
        }

        (new EntityWriter($this->orm))->write([$campaign]);

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

        (new EntityWriter($this->orm))->delete([$campaign]);

        return true;
    }
}
