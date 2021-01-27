<?php

namespace Mailery\Campaign\Regular\Form;

use FormManager\Factory as F;
use FormManager\Form;
use Mailery\Brand\Entity\Brand;
use Mailery\Brand\Service\BrandLocatorInterface as BrandLocator;
use Mailery\Campaign\Regular\Entity\RegularCampaign;
use Mailery\Campaign\Repository\CampaignRepository;
use Mailery\Campaign\Regular\Service\CampaignCrudService;
use Mailery\Campaign\Regular\ValueObject\CampaignValueObject;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Mailery\Template\Repository\TemplateRepository;
use Mailery\Subscriber\Counter\SubscriberCounter;
use Mailery\Subscriber\Entity\Group;
use Mailery\Subscriber\Repository\GroupRepository;
use Spiral\Database\Injection\Parameter;

class CampaignForm extends Form
{
    /**
     * @var Brand
     */
    private Brand $brand;

    /**
     * @var RegularCampaign|null
     */
    private ?RegularCampaign $campaign = null;

    /**
     * @var CampaignRepository
     */
    private CampaignRepository $campaignRepo;

    /**
     * @var TemplateRepository
     */
    private TemplateRepository $templateRepo;

    /**
     * @var GroupRepository
     */
    private GroupRepository $groupRepo;

    /**
     * @var CampaignCrudService
     */
    private CampaignCrudService $campaignCrudService;

    /**
     * @var SubscriberCounter
     */
    private SubscriberCounter $subscriberCounter;

    /**
     * @param BrandLocator $brandLocator
     * @param CampaignRepository $campaignRepo
     * @param TemplateRepository $templateRepo
     * @param GroupRepository $groupRepo
     * @param CampaignCrudService $campaignCrudService
     * @param SubscriberCounter $subscriberCounter
     */
    public function __construct(
        BrandLocator $brandLocator,
        CampaignRepository $campaignRepo,
        TemplateRepository $templateRepo,
        GroupRepository $groupRepo,
        CampaignCrudService $campaignCrudService,
        SubscriberCounter $subscriberCounter
    ) {
        $this->brand = $brandLocator->getBrand();
        $this->campaignRepo = $campaignRepo->withBrand($this->brand);
        $this->templateRepo = $templateRepo->withBrand($this->brand);
        $this->groupRepo = $groupRepo->withBrand($this->brand);
        $this->campaignCrudService = $campaignCrudService;
        $this->subscriberCounter = $subscriberCounter->withBrand($this->brand);
        parent::__construct($this->inputs());
    }

    /**
     * @param string $csrf
     * @return \self
     */
    public function withCsrf(string $value, string $name = '_csrf'): self
    {
        $this->offsetSet($name, F::hidden($value));

        return $this;
    }

    /**
     * @param RegularCampaign $campaign
     * @return self
     */
    public function withCampaign(RegularCampaign $campaign): self
    {
        $this->campaign = $campaign;
        $this->offsetSet('', F::submit('Update'));

        $this['name']->setValue($campaign->getName());
        $this['template']->setValue($campaign->getTemplate()->getId());
        $this['groups']->setValue(array_map(
            function (Group $group) {
                return $group->getId();
            },
            $campaign->getGroups()->toArray()
        ));

        return $this;
    }

    /**
     * @return RegularCampaign|null
     */
    public function save(): ?RegularCampaign
    {
        if (!$this->isValid()) {
            return null;
        }

        $templateId = $this['template']->getValue();
        $template = $this->templateRepo->findByPK($templateId);

        $groupIds = $this['groups']->getValue();
        $groups = $this->groupRepo->findAll([
            'id' => ['in' => new Parameter($groupIds)],
        ]);

        $valueObject = CampaignValueObject::fromForm($this)
            ->withBrand($this->brand)
            ->withTemplate($template)
            ->withGroups($groups);

        if (($campaign = $this->campaign) === null) {
            $campaign = $this->campaignCrudService->create($valueObject);
        } else {
            $this->campaignCrudService->update($campaign, $valueObject);
        }

        return $campaign;
    }

    /**
     * @return array
     */
    private function inputs(): array
    {
        $nameConstraint = new Constraints\Callback([
            'callback' => function ($value, ExecutionContextInterface $context) {
                if (empty($value)) {
                    return;
                }

                $campaign = $this->campaignRepo->findByName($value, $this->campaign);
                if ($campaign !== null) {
                    $context->buildViolation('Campaign with this name already exists.')
                        ->atPath('name')
                        ->addViolation();
                }
            },
        ]);

        $templateOptions = $this->getTemplateOptions();
        $groupOptions = $this->getGroupOptions();

        return [
            'name' => F::text('Campaign name')
                ->addConstraint(new Constraints\NotBlank())
                ->addConstraint(new Constraints\Length([
                    'min' => 4,
                ]))
                ->addConstraint($nameConstraint),
            'template' => F::select('Template', $templateOptions)
                ->addConstraint(new Constraints\NotBlank())
                ->addConstraint(new Constraints\Choice([
                    'choices' => array_keys($templateOptions)
                ])),
            'groups' => F::select('Send to groups', $groupOptions, ['multiple' => true])
                ->addConstraint(new Constraints\NotBlank())
                ->addConstraint(new Constraints\Choice([
                    'choices' => array_keys($groupOptions),
                    'multiple' => true,
                ])),
            '' => F::submit($this->campaign === null ? 'Create' : 'Update'),
        ];
    }

    /**
     * @return array
     */
    private function getTemplateOptions(): array
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
    private function getGroupOptions(): array
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
