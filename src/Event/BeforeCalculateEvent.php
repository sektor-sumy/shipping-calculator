<?php

namespace EsteIt\ShippingCalculator\Event;

use EsteIt\ShippingCalculator\Calculator\CalculatorInterface;
use EsteIt\ShippingCalculator\Model\PackageInterface;
use Symfony\Component\EventDispatcher\Event;

class BeforeCalculateEvent extends Event
{
    /**
     * @var CalculatorInterface
     */
    protected $calculator;

    /**
     * @var PackageInterface
     */
    protected $package;

    /**
     * @param CalculatorInterface $calculator
     * @param PackageInterface $package
     */
    public function __construct(CalculatorInterface $calculator, PackageInterface $package)
    {
        $this->calculator = $calculator;
        $this->package = $package;
    }

    /**
     * @return CalculatorInterface
     */
    public function getCalculator()
    {
        return $this->calculator;
    }

    /**
     * @return PackageInterface
     */
    public function getPackage()
    {
        return $this->package;
    }
}
