<?php

namespace Mailery\Campaign\Regular\Form;

use Cycle\ORM\ORMInterface;
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

class CampaignForm extends Form
{
    /**
     * @var Brand
     */
    private Brand $brand;

    /**
     * @var ORMInterface
     */
    private ORMInterface $orm;

    /**
     * @var RegularCampaign|null
     */
    private ?RegularCampaign $campaign;

    /**
     * @var CampaignCrudService
     */
    private CampaignCrudService $campaignCrudService;

    /**
     * @param BrandLocator $brandLocator
     * @param CampaignCrudService $campaignCrudService
     * @param ORMInterface $orm
     */
    public function __construct(
        BrandLocator $brandLocator,
        CampaignCrudService $campaignCrudService,
        ORMInterface $orm
    ) {
        $this->orm = $orm;
        $this->brand = $brandLocator->getBrand();
        $this->campaignCrudService = $campaignCrudService;
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

        $valueObject = CampaignValueObject::fromForm($this)
            ->withBrand($this->brand);

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

                $campaign = $this->getCampaignRepository()->findByName($value, $this->campaign);
                if ($campaign !== null) {
                    $context->buildViolation('Campaign with this name already exists.')
                        ->atPath('name')
                        ->addViolation();
                }
            },
        ]);

        return [
            'name' => F::text('Campaign name')
                ->addConstraint(new Constraints\NotBlank())
                ->addConstraint(new Constraints\Length([
                    'min' => 4,
                ]))
                ->addConstraint($nameConstraint),
            '' => F::submit($this->campaign === null ? 'Create' : 'Update'),
        ];
    }

    /**
     * @return CampaignRepository
     */
    private function getCampaignRepository(): CampaignRepository
    {
        return $this->orm->getRepository(RegularCampaign::class)
            ->withBrand($this->brand);
    }
}
