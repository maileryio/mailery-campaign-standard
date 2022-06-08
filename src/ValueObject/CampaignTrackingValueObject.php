<?php

namespace Mailery\Campaign\Standard\ValueObject;

use Mailery\Campaign\Standard\Form\CampaignTrackingForm;

class CampaignTrackingValueObject
{

    /**
     * @var bool
     */
    private bool $trackClicks;

    /**
     * @var bool
     */
    private bool $trackOpens;

    /**
     * @var bool
     */
    private bool $enableUtmTags;

    /**
     * @param CampaignTrackingForm $form
     * @return self
     */
    public static function fromForm(CampaignTrackingForm $form): self
    {
        $new = new self();
        $new->trackClicks = $form->getTrackClicks();
        $new->trackOpens = $form->getTrackOpens();
        $new->enableUtmTags = $form->getEnableUtmTags();

        return $new;
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

}
