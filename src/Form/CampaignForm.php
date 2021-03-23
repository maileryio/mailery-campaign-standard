<?php

namespace Mailery\Campaign\Standard\Form;

use Yiisoft\Form\FormModel;
use Mailery\Brand\BrandLocatorInterface as BrandLocator;
use Mailery\Campaign\Standard\Entity\StandardCampaign;
use Mailery\Template\Repository\TemplateRepository;
use Mailery\Subscriber\Counter\SubscriberCounter;
use Mailery\Subscriber\Entity\Group;
use Mailery\Subscriber\Repository\GroupRepository;
use Spiral\Database\Injection\Parameter;
use Yiisoft\Form\HtmlOptions\RequiredHtmlOptions;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Form\HtmlOptions\HasLengthHtmlOptions;
use Yiisoft\Validator\Rule\HasLength;
use Mailery\Sender\Repository\SenderRepository;
use Mailery\Sender\Model\Status;
use Mailery\Template\Entity\Template;
use Mailery\Sender\Entity\Sender;

class CampaignForm extends FormModel
{
    /**
     * @var string|null
     */
    private ?string $name = null;

    /**
     * @var int|null
     */
    private ?int $sender = null;

    /**
     * @var int|null
     */
    private ?int $template = null;

    /**
     * @var array
     */
    private array $groups = [];

    /**
     * @var SenderRepository
     */
    private SenderRepository $senderRepo;

    /**
     * @var TemplateRepository
     */
    private TemplateRepository $templateRepo;

    /**
     * @var GroupRepository
     */
    private GroupRepository $groupRepo;

    /**
     * @param BrandLocator $brandLocator
     * @param SenderRepository $senderRepo
     * @param TemplateRepository $templateRepo
     * @param GroupRepository $groupRepo
     * @param SubscriberCounter $subscriberCounter
     */
    public function __construct(
        BrandLocator $brandLocator,
        SenderRepository $senderRepo,
        TemplateRepository $templateRepo,
        GroupRepository $groupRepo,
        SubscriberCounter $subscriberCounter
    ) {
        $this->senderRepo = $senderRepo->withBrand($brandLocator->getBrand());
        $this->templateRepo = $templateRepo->withBrand($brandLocator->getBrand());
        $this->groupRepo = $groupRepo->withBrand($brandLocator->getBrand());
        $this->subscriberCounter = $subscriberCounter->withBrand($brandLocator->getBrand());

        parent::__construct();
    }

    /**
     * @param StandardCampaign $campaign
     * @return self
     */
    public function withCampaign(StandardCampaign $campaign): self
    {
        $new = clone $this;
        $new->name = $campaign->getName();
        $new->sender = $campaign->getSender()->getId();
        $new->template = $campaign->getTemplate()->getId();
        $new->groups = $campaign->getGroups()->map(fn (Group $group) => $group->getId())->toArray();

        return $new;
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'name' => 'Name',
            'sender' => 'Sender',
            'template' => 'Template',
            'groups' => 'Groups',
        ];
    }

    /**
     * @return string
     */
    public function formName(): string
    {
        return 'CampaignForm';
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return [
            'name' => [
                new RequiredHtmlOptions(new Required()),
                new HasLengthHtmlOptions((new HasLength())->max(255)),
            ],
            'sender' => [
                new RequiredHtmlOptions(new Required()),
            ],
            'template' => [
                new RequiredHtmlOptions(new Required()),
            ],
            'groups' => [
                new RequiredHtmlOptions(new Required()),
            ],
        ];
    }

    /**
     * @return Sender|null
     */
    public function getSender(): ?Sender
    {
        if ($this->sender === null) {
            return null;
        }

        return $this->senderRepo->findByPK($this->sender);
    }

    /**
     * @return array
     */
    public function getSenderOptions(): array
    {
        $options = [];
        $senders = $this->senderRepo
            ->withStatus(Status::asActive())
            ->findAll();

        foreach ($senders as $sender) {
            $options[$sender->getId()] = $sender->getName();
        }

        return $options;
    }

    /**
     * @return Template|null
     */
    public function getTemplate(): ?Template
    {
        if ($this->template === null) {
            return null;
        }

        return $this->templateRepo->findByPK($this->template);
    }

    /**
     * @return array
     */
    public function getTemplateOptions(): array
    {
        $options = [];
        $templates = $this->templateRepo->findAll();

        foreach ($templates as $template) {
            $options[$template->getId()] = $template->getName();
        }

        return $options;
    }

    /**
     * @return array
     */
    public function getGroups(): array
    {
        if (empty($this->groups)) {
            return [];
        }

        return $this->groupRepo->findAll([
            'id' => ['in' => new Parameter($this->groups, \PDO::PARAM_INT)],
        ]);
    }

    /**
     * @return array
     */
    public function getGroupOptions(): array
    {
        $options = [];
        $groups = $this->groupRepo->findAll();

        foreach ($groups as $group) {
            $options[$group->getId()] = sprintf(
                '%s (%d)',
                $group->getName(),
                $this->subscriberCounter->withGroup($group)->getActiveCount()
            );
        }

        return $options;
    }
}
