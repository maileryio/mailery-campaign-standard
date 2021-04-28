<?php

namespace Mailery\Campaign\Standard\ValueObject;

use Mailery\Campaign\Standard\Form\CampaignForm;
use Mailery\Template\Entity\Template;
use Mailery\Sender\Entity\Sender;
use Mailery\Subscriber\Entity\Group;
use Mailery\Channel\ChannelInterface as Channel;

class CampaignValueObject
{
    /**
     * @var string|null
     */
    private ?string $name;

    /**
     * @var Sender|null
     */
    private ?Sender $sender;

    /**
     * @var Template|null
     */
    private ?Template $template;

    /**
     * @var Channel
     */
    private Channel $channel;

    /**
     * @var Group[]
     */
    private array $groups = [];

    /**
     * @param CampaignForm $form
     * @return self
     */
    public static function fromForm(CampaignForm $form): self
    {
        $new = new self();

        $new->name = $form->getAttributeValue('name');
        $new->sender = $form->getSender();
        $new->template = $form->getTemplate();
        $new->channel = $form->getChannel();
        $new->groups = $form->getGroups();

        return $new;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return Sender|null
     */
    public function getSender(): ?Sender
    {
        return $this->sender;
    }

    /**
     * @return Template|null
     */
    public function getTemplate(): ?Template
    {
        return $this->template;
    }

    /**
     * @return Channel
     */
    public function getChannel(): Channel
    {
        return $this->channel;
    }

    /**
     * @return Group[]
     */
    public function getGroups(): array
    {
        return $this->groups;
    }
}
