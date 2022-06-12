<?php

namespace Mailery\Campaign\Standard\Form;

use Mailery\Campaign\Field\UtmTags;
use Mailery\Campaign\Standard\Entity\StandardCampaign as Campaign;
use Yiisoft\Form\FormModel;
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
     * @var string|null
     */
    private ?string $utmSource = null;

    /**
     * @var string|null
     */
    private ?string $utmMedium = null;

    /**
     * @var string|null
     */
    private ?string $utmCampaign = null;

    /**
     * @var string|null
     */
    private ?string $utmContent = null;

    /**
     * @var string|null
     */
    private ?string $utmTerm = null;

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

        if (($utmTags = $entity->getUtmTags()) !== null) {
            $new->utmSource = $entity->getUtmTags()->getSource();
            $new->utmMedium = $entity->getUtmTags()->getMedium();
            $new->utmCampaign = $entity->getUtmTags()->getCampaign();
            $new->utmContent = $entity->getUtmTags()->getContent();
            $new->utmTerm = $entity->getUtmTags()->getTerm();
        }

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
     * @return UtmTags|null
     */
    public function getUtmTags(): ?UtmTags
    {
        if ($this->enableUtmTags === false) {
            return null;
        }

        return (new UtmTags())
            ->setSource($this->utmSource)
            ->setMedium($this->utmMedium)
            ->setCampaign($this->utmCampaign)
            ->setContent($this->utmContent)
            ->setTerm($this->utmTerm);
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
            'utmSource' => 'UTM_Source',
            'utmMedium' => 'UTM_Medium',
            'utmCampaign' => 'UTM_Campaign',
            'utmContent' => 'UTM_Content',
            'utmTerm' => 'UTM_Term',
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
