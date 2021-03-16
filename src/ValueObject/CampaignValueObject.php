<?php

namespace Mailery\Campaign\Standard\ValueObject;

use Mailery\Campaign\Standard\Form\CampaignForm;
use Mailery\Template\Entity\Template;
use Mailery\Subscriber\Entity\Group;

class CampaignValueObject
{
    /**
     * @var string
     */
    private string $name;

    /**
     * @var Template
     */
    private Template $template;

    /**
     * @var Group[]
     */
    private array $groups;

    /**
     * @param CampaignForm $form
     * @return self
     */
    public static function fromForm(CampaignForm $form): self
    {
        $new = new self();

        $new->name = $form->getAttributeValue('name');

        return $new;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Template
     */
    public function getTemplate(): Template
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

    /**
     * @param Template $template
     * @return self
     */
    public function withTemplate(Template $template): self
    {
        $new = clone $this;
        $new->template = $template;

        return $new;
    }

    /**
     * @param Group[] $groups
     * @return self
     */
    public function withGroups(array $groups): self
    {
        $new = clone $this;
        $new->groups = $groups;

        return $new;
    }
}
