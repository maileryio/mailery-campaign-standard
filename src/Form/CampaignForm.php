<?php

namespace Mailery\Campaign\Standard\Form;

use Yiisoft\Form\FormModel;
use Mailery\Brand\Entity\Brand;
use Mailery\Brand\BrandLocatorInterface as BrandLocator;
use Mailery\Campaign\Standard\Entity\StandardCampaign;
use Mailery\Campaign\Repository\CampaignRepository;
use Mailery\Campaign\Standard\Service\CampaignCrudService;
use Mailery\Campaign\Standard\ValueObject\CampaignValueObject;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Mailery\Template\Repository\TemplateRepository;
use Mailery\Subscriber\Counter\SubscriberCounter;
use Mailery\Subscriber\Entity\Group;
use Mailery\Subscriber\Repository\GroupRepository;
use Spiral\Database\Injection\Parameter;
use Yiisoft\Form\HtmlOptions\RequiredHtmlOptions;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Form\HtmlOptions\HasLengthHtmlOptions;
use Yiisoft\Validator\Rule\HasLength;

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
    private array $recipients = [];

    /**
     * @var TemplateRepository
     */
    private TemplateRepository $templateRepo;

    /**
     * @param BrandLocator $brandLocator
     * @param TemplateRepository $templateRepo
     * @param GroupRepository $groupRepo
     * @param SubscriberCounter $subscriberCounter
     */
    public function __construct(
        BrandLocator $brandLocator,
        TemplateRepository $templateRepo,
        GroupRepository $groupRepo,
        SubscriberCounter $subscriberCounter
    ) {
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
        $new->recipients = $campaign->getGroups()->map(fn (Group $group) => $group->getId());

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
            'recipients' => 'Recipients',
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
            'recipients' => [
                new RequiredHtmlOptions(new Required()),
            ],
        ];
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




//
//    /**
//     * @var Brand
//     */
//    private Brand $brand;
//
//    /**
//     * @var StandardCampaign|null
//     */
//    private ?StandardCampaign $campaign = null;
//
//    /**
//     * @var CampaignRepository
//     */
//    private CampaignRepository $campaignRepo;
//
//    /**
//     * @var GroupRepository
//     */
//    private GroupRepository $groupRepo;
//
//    /**
//     * @var CampaignCrudService
//     */
//    private CampaignCrudService $campaignCrudService;
//
//    /**
//     * @var SubscriberCounter
//     */
//    private SubscriberCounter $subscriberCounter;
//
//    /**
//     * @param BrandLocator $brandLocator
//     * @param CampaignRepository $campaignRepo
//     * @param TemplateRepository $templateRepo
//     * @param GroupRepository $groupRepo
//     * @param CampaignCrudService $campaignCrudService
//     * @param SubscriberCounter $subscriberCounter
//     */
//    public function __construct(
//        BrandLocator $brandLocator,
//        CampaignRepository $campaignRepo,
//        TemplateRepository $templateRepo,
//        GroupRepository $groupRepo,
//        CampaignCrudService $campaignCrudService,
//        SubscriberCounter $subscriberCounter
//    ) {
//        $this->brand = $brandLocator->getBrand();
//        $this->campaignRepo = $campaignRepo->withBrand($this->brand);
//        $this->templateRepo = $templateRepo->withBrand($this->brand);
//        $this->groupRepo = $groupRepo->withBrand($this->brand);
//        $this->campaignCrudService = $campaignCrudService;
//        $this->subscriberCounter = $subscriberCounter->withBrand($this->brand);
//    }
//
//    /**
//     * @return StandardCampaign|null
//     */
//    public function save(): ?StandardCampaign
//    {
//        if (!$this->isValid()) {
//            return null;
//        }
//
//        $templateId = $this['template']->getValue();
//        $template = $this->templateRepo->findByPK($templateId);
//
//        $groupIds = $this['groups']->getValue();
//        $groups = $this->groupRepo->findAll([
//            'id' => ['in' => new Parameter($groupIds)],
//        ]);
//
//        $valueObject = CampaignValueObject::fromForm($this)
//            ->withBrand($this->brand)
//            ->withTemplate($template)
//            ->withGroups($groups);
//
//        if (($campaign = $this->campaign) === null) {
//            $campaign = $this->campaignCrudService->create($valueObject);
//        } else {
//            $this->campaignCrudService->update($campaign, $valueObject);
//        }
//
//        return $campaign;
//    }
//
//    /**
//     * @return array
//     */
//    private function inputs(): array
//    {
//        $nameConstraint = new Constraints\Callback([
//            'callback' => function ($value, ExecutionContextInterface $context) {
//                if (empty($value)) {
//                    return;
//                }
//
//                $campaign = $this->campaignRepo->findByName($value, $this->campaign);
//                if ($campaign !== null) {
//                    $context->buildViolation('Campaign with this name already exists.')
//                        ->atPath('name')
//                        ->addViolation();
//                }
//            },
//        ]);
//
//        $templateOptions = $this->getTemplateOptions();
//        $groupOptions = $this->getGroupOptions();
//
//        return [
//            'name' => F::text('Campaign name')
//                ->addConstraint(new Constraints\NotBlank())
//                ->addConstraint(new Constraints\Length([
//                    'min' => 4,
//                ]))
//                ->addConstraint($nameConstraint),
//            'template' => F::select('Template', $templateOptions)
//                ->addConstraint(new Constraints\NotBlank())
//                ->addConstraint(new Constraints\Choice([
//                    'choices' => array_keys($templateOptions)
//                ])),
//            'recipients' => F::select('Recipients', $groupOptions, ['multiple' => true])
//                ->addConstraint(new Constraints\NotBlank())
//                ->addConstraint(new Constraints\Choice([
//                    'choices' => array_keys($groupOptions),
//                    'multiple' => true,
//                ])),
//            '' => F::submit($this->campaign === null ? 'Create' : 'Update'),
//        ];
//    }
}
