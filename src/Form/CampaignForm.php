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
use Yiisoft\Validator\Rule\InRange;
use Mailery\Sender\Repository\SenderRepository;
use Mailery\Sender\Model\Status;
use Mailery\Template\Entity\Template;
use Mailery\Sender\Entity\Sender;
use Mailery\Channel\Repository\ChannelRepository;
use Mailery\Channel\Entity\Channel;

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
     * @var ChannelRepository
     */
    private ChannelRepository $channelRepo;

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
     * @param ChannelRepository $channelRepo
     * @param SenderRepository $senderRepo
     * @param TemplateRepository $templateRepo
     * @param GroupRepository $groupRepo
     * @param SubscriberCounter $subscriberCounter
     */
    public function __construct(
        BrandLocator $brandLocator,
        ChannelRepository $channelRepo,
        SenderRepository $senderRepo,
        TemplateRepository $templateRepo,
        GroupRepository $groupRepo,
        SubscriberCounter $subscriberCounter
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
                new RequiredHtmlOptions(Required::rule()),
                new HasLengthHtmlOptions(HasLength::rule()->max(255)),
            ],
            'channel' => [
                new RequiredHtmlOptions(Required::rule()),
                InRange::rule(array_keys($this->getChannelListOptions())),
            ],
            'sender' => [
                new RequiredHtmlOptions(Required::rule()),
            ],
            'template' => [
                new RequiredHtmlOptions(Required::rule()),
            ],
            'groups' => [
                new RequiredHtmlOptions(Required::rule()),
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
    public function getSenderListOptions(): array
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
