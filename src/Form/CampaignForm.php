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
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\InRange;
use Mailery\Sender\Repository\SenderRepository;
use Mailery\Sender\Field\SenderStatus;
use Mailery\Template\Entity\Template;
use Mailery\Sender\Entity\Sender;
use Mailery\Channel\Repository\ChannelRepository;
use Mailery\Channel\Entity\Channel;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\RuleSet;

class CampaignForm extends FormModel
{
    /**
     * @var string|null
     */
    private ?string $name = null;

    /**
     * @var string|null
     */
    private ?string $channel = null;

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
     * @param ChannelRepository $channelRepo
     * @param SenderRepository $senderRepo
     * @param TemplateRepository $templateRepo
     * @param GroupRepository $groupRepo
     * @param SubscriberCounter $subscriberCounter
     * @param BrandLocator $brandLocator
     */
    public function __construct(
        private ChannelRepository $channelRepo,
        private SenderRepository $senderRepo,
        private TemplateRepository $templateRepo,
        private GroupRepository $groupRepo,
        private SubscriberCounter $subscriberCounter,
        BrandLocator $brandLocator
    ) {
        $this->channelRepo = $channelRepo->withBrand($brandLocator->getBrand());
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
    public function withEntity(StandardCampaign $campaign): self
    {
        $new = clone $this;
        $new->name = $campaign->getName();
        $new->channel = $campaign->getChannel()->getId();
        $new->sender = $campaign->getSender()->getId();
        $new->template = $campaign->getTemplate()->getId();
        $new->groups = $campaign->getGroups()->map(
            fn (Group $group) => $group->getId()
        )->toArray();

        return $new;
    }

    /**
     * @inheritdoc
     */
    public function load(array $data, ?string $formName = null): bool
    {
        $scope = $formName ?? $this->getFormName();

        if (isset($data[$scope]['groups'])) {
            $data[$scope]['groups'] = array_filter((array) $data[$scope]['groups']);
        }

        return parent::load($data, $formName);
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return Channel|null
     */
    public function getChannel(): ?Channel
    {
        if ($this->channel === null) {
            return null;
        }

        return $this->channelRepo->findByPK($this->channel);
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
    public function getAttributeLabels(): array
    {
        return [
            'name' => 'Subject',
            'channel' => 'Channel',
            'sender' => 'Sender',
            'template' => 'Template',
            'groups' => 'Groups',
        ];
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return [
            'name' => [
                Required::rule(),
                HasLength::rule()->min(3)->max(255),
            ],
            'channel' => [
                Required::rule(),
                InRange::rule(array_keys($this->getChannelListOptions())),
            ],
            'sender' => [
                Required::rule(),
            ],
            'template' => [
                Required::rule(),
            ],
            'groups' => [
                Required::rule(),
                Each::rule(new RuleSet([
                    InRange::rule(array_keys($this->getGroupListOptions())),
                ]))->message('{error}'),
            ],
        ];
    }

    /**
     * @return array
     */
    public function getSenderListOptions(): array
    {
        $options = [];
        $senders = $this->senderRepo->withActive()->findAll();

        foreach ($senders as $sender) {
            $options[$sender->getId()] = $sender->getName();
        }

        return $options;
    }

    /**
     * @return array
     */
    public function getTemplateListOptions(): array
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
    public function getGroupListOptions(): array
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

    /**
     * @return array
     */
    public function getChannelListOptions(): array
    {
        $listOptions = [];
        foreach ($this->channelRepo->findAll() as $channel) {
            /** @var Channel $channel */
            $listOptions[$channel->getId()] = $channel->getName();
        }

        return $listOptions;
    }
}
