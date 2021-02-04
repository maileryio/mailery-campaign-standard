<?php

namespace Mailery\Campaign\Standard\Entity;

use Mailery\Campaign\Entity\Campaign;
use Mailery\Activity\Log\Entity\LoggableEntityInterface;
use Mailery\Activity\Log\Entity\LoggableEntityTrait;
use Mailery\Common\Entity\RoutableEntityInterface;

/**
 * @Cycle\Annotated\Annotation\Entity
 */
class StandardCampaign extends Campaign implements RoutableEntityInterface, LoggableEntityInterface
{
    use LoggableEntityTrait;

    /**
     * {@inheritdoc}
     */
    public function getEditRouteName(): ?string
    {
        return '/campaign/standard/edit';
    }

    /**
     * {@inheritdoc}
     */
    public function getEditRouteParams(): array
    {
        return ['id' => $this->getId()];
    }

    /**
     * {@inheritdoc}
     */
    public function getViewRouteName(): ?string
    {
        return '/campaign/standard/view';
    }

    /**
     * {@inheritdoc}
     */
    public function getViewRouteParams(): array
    {
        return ['id' => $this->getId()];
    }

    /**
     * {@inheritdoc}
     */
    public function getPreviewRouteName(): ?string
    {
        return '/campaign/standard/view';
    }

    /**
     * {@inheritdoc}
     */
    public function getPreviewRouteParams(): array
    {
        return ['id' => $this->getId()];
    }
}
