<?php

namespace EsteIt\ShippingCalculator\CalculatorHandler\Asendia;

use EsteIt\ShippingCalculator\Exception\InvalidWeightException;
use Moriony\Trivial\Math\MathInterface;
use Moriony\Trivial\Math\NativeMath;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ZoneCalculator
{
    /**
     * @var MathInterface
     */
    protected $math;

    public function __construct(array $options)
    {
        $resolver = new OptionsResolver();
        $resolver
            ->setDefaults([
                'math' => new NativeMath(),
            ])
            ->setRequired([
                'math',
                'name',
                'weight_prices'
            ])
            ->setAllowedTypes([
                'math' => 'Moriony\Trivial\Math\MathInterface',
                'weight_prices' => 'array',
            ]);

        $weightPricesNormalizer = function (Options $options, $weightPrices) {
            $normalized = [];
            /** @var ZoneCalculator $calculator */
            foreach ($weightPrices as $weightPrice) {
                $normalized[$weightPrice['weight']] = $weightPrice['price'];
            }
            return $normalized;
        };

        $resolver->setNormalizer('weight_prices', $weightPricesNormalizer);

        $this->options = $resolver->resolve($options);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->options['name'];
    }

    /**
     * @return MathInterface
     */
    public function getMath()
    {
        if (!$this->math) {
            $this->math = new NativeMath();
        }

        return $this->math;
    }

    /**
     * @param string|int|float $weight
     * @return mixed
     */
    public function calculate($weight)
    {
        if (!is_scalar($weight)) {
            throw new InvalidWeightException('Weight should be a scalar value.');
        }

        $math = $this->getMath();
        if ($math->lessThan($weight, 0)) {
            throw new InvalidWeightException('Weight should be greater than zero.');
        }

        $currentWeight = null;
        $price = null;
        $math = $this->getMath();

        foreach ($this->options['weight_prices'] as $w => $p) {
            if ($math->lessOrEqualThan($weight, $w) && $math->greaterThan($weight, $currentWeight)) {
                $currentWeight = $w;
                $price = $p;
            }
        }

        if (is_null($price)) {
            throw new InvalidWeightException('Can not calculate shipping for this weight.');
        }

        return $price;
    }
}
