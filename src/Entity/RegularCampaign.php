<?php

namespace Mailery\Campaign\Regular\Entity;

use Mailery\Campaign\Entity\Campaign;
use Mailery\Activity\Log\Entity\LoggableEntityInterface;
use Mailery\Activity\Log\Entity\LoggableEntityTrait;
use Mailery\Common\Entity\RoutableEntityInterface;

/**
 * @Cycle\Annotated\Annotation\Entity
 */
class RegularCampaign extends Campaign implements RoutableEntityInterface, LoggableEntityInterface
{
    use LoggableEntityTrait;

    /**
     * {@inheritdoc}
     */
    public function getEditRouteName(): ?string
    {
        return '/campaign/regular/edit';
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
        return '/campaign/regular/view';
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
        return '/campaign/regular/view';
    }

    /**
     * {@inheritdoc}
     */
    public function getPreviewRouteParams(): array
    {
        return ['id' => $this->getId()];
    }
}
