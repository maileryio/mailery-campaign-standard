<?php

namespace Mailery\Campaign\Regular\ValueObject;

use Mailery\Brand\Entity\Brand;
use Mailery\Campaign\Regular\Form\CampaignForm;

class CampaignValueObject
{
    /**
     * @var string
     */
    private string $name;

    /**
     * @var Brand
     */
    private Brand $brand;

    /**
     * @param CampaignForm $form
     * @return self
     */
    public static function fromForm(CampaignForm $form): self
    {
        $new = new self();

        $new->name = $form['name']->getValue();

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
     * @return Brand
     */
    public function getBrand(): Brand
    {
        return $this->brand;
    }

    /**
     * @param Brand $brand
     * @return self
     */
    public function withBrand(Brand $brand): self
    {
        $new = clone $this;
        $new->brand = $brand;

        return $new;
    }
}
