<?php

namespace Mailery\Campaign\Standard\Service;

use Cycle\ORM\ORMInterface;
use Mailery\Campaign\Service\CampaignCrudService as BaseCrudService;
use Mailery\Campaign\Standard\Entity\StandardCampaign;
use Mailery\Campaign\ValueObject\CampaignValueObject;
use Mailery\Brand\Entity\Brand;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

class CampaignCrudService extends BaseCrudService
{
    /**
     * @var Brand
     */
    private Brand $brand;

    /**
     * @param ORMInterface $orm
     */
    public function __construct(
        private ORMInterface $orm
    ) {
        parent::__construct($orm);
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
            ->setBrand($this->brand)
            ->setName($valueObject->getName())
            ->setSender($valueObject->getSender())
            ->setTemplate($valueObject->getTemplate())
            ->setStatus($valueObject->getStatus())
        ;
        foreach ($valueObject->getGroups() as $group) {
            $campaign->getGroups()->add($group);
        }

        (new EntityWriter($this->orm))->write([$campaign]);

        return $campaign;
    }
}
