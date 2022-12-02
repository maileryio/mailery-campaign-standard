<?php

namespace Mailery\Campaign\Standard\Entity;

use Mailery\Campaign\Entity\Campaign;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Inheritance\SingleTable;
use Mailery\Common\Entity\RoutableEntityInterface;
use Mailery\Activity\Log\Entity\LoggableEntityInterface;
use Mailery\Activity\Log\Entity\LoggableEntityTrait;
use Mailery\Campaign\Standard\Model\StandardCampaignType;

#[Entity(
    table: 'campaigns',
)]
#[SingleTable(value: StandardCampaignType::class)]
class StandardCampaign extends Campaign implements RoutableEntityInterface, LoggableEntityInterface
{
    use LoggableEntityTrait;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    public function getIndexRouteName(): ?string
    {
        return '/campaign/default/index';
    }

    /**
     * @inheritdoc
     */
    public function getIndexRouteParams(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getViewRouteName(): ?string
    {
        return '/campaign/standard/view';
    }

    /**
     * @inheritdoc
     */
    public function getViewRouteParams(): array
    {
        return ['id' => $this->getId()];
    }

    /**
     * @inheritdoc
     */
    public function getEditRouteName(): ?string
    {
        return '/campaign/standard/edit';
    }

    /**
     * @inheritdoc
     */
    public function getEditRouteParams(): array
    {
        return ['id' => $this->getId()];
    }

    /**
     * @inheritdoc
     */
    public function getDeleteRouteName(): ?string
    {
        return '/campaign/standard/delete';
    }

    /**
     * @inheritdoc
     */
    public function getDeleteRouteParams(): array
    {
        return ['id' => $this->getId()];
    }

    /**
     * @inheritdoc
     */
    public function getPreviewRouteName(): string
    {
        return '/campaign/standard/preview';
    }

    /**
     * @inheritdoc
     */
    public function getPreviewRouteParams(): array
    {
        return ['id' => $this->getId()];
    }

}
