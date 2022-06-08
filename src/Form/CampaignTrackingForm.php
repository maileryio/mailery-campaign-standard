<?php

namespace Mailery\Campaign\Standard\Form;

use Yiisoft\Form\FormModel;
use Mailery\Campaign\Standard\Entity\StandardCampaign as Campaign;
use Yiisoft\Validator\Rule\Required;

class CampaignTrackingForm extends FormModel
{

    /**
     * @var bool
     */
    private bool $trackClicks = false;

    /**
     * @var bool
     */
    private bool $trackOpens = false;

    /**
     * @var bool
     */
    private bool $enableUtmTags = false;

    /**
     * @var Campaign|null
     */
    private ?Campaign $entity = null;

    /**
     * @param Campaign $entity
     * @return self
     */
    public function withEntity(Campaign $entity): self
    {
        $new = clone $this;
        $new->entity = $entity;
        $new->trackClicks = $entity->getTrackClicks();
        $new->trackOpens = $entity->getTrackOpens();
        $new->enableUtmTags = $entity->getEnableUtmTags();

        return $new;
    }

    /**
     * @return bool
     */
    public function hasEntity(): bool
    {
        return $this->entity !== null;
    }

    /**
     * @return bool
     */
    public function getTrackClicks(): bool
    {
        return $this->trackClicks;
    }

    /**
     * @return bool
     */
    public function getTrackOpens(): bool
    {
        return $this->trackOpens;
    }

    /**
     * @return bool
     */
    public function getEnableUtmTags(): bool
    {
        return $this->enableUtmTags;
    }

    /**
     * @return array
     */
    public function getAttributeLabels(): array
    {
        return [
            'trackClicks' => 'Track clicks',
            'trackOpens' => 'Track opens',
            'enableUtmTags' => 'Enable UTM tags',
        ];
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return [
            'trackClicks' => [
                Required::rule(),
            ],
            'trackOpens' => [
                Required::rule(),
            ],
            'enableUtmTags' => [
                Required::rule(),
            ],
        ];
    }

    /**
     * @return array
     */
    public function getBooleanListOptions(): array
    {
        return [
            0 => 'No',
            1 => 'Yes',
        ];
    }

}
