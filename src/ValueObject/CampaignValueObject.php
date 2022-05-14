<?php

namespace Mailery\Campaign\Standard\ValueObject;

use Mailery\Campaign\Standard\Form\CampaignForm;
use Mailery\Template\Entity\Template;
use Mailery\Sender\Entity\Sender;
use Mailery\Subscriber\Entity\Group;

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
        $new->name = $form->getName();
        $new->sender = $form->getSender();
        $new->template = $form->getTemplate();
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
     * @return Group[]
     */
    public function getGroups(): array
    {
        return $this->groups;
    }
}
