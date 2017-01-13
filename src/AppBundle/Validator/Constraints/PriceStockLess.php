<?php

namespace AppBundle\Validator\Constraints;


use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PriceStockLess extends Constraint
{
    /**
     * Violation message
     *
     * @var string
     */
    public $message = 'If price less than "{{ minPrice }}" '.
    'stock can not be less than "{{ minStock }}"';

    /**
     * Minimal limit for price
     *
     * @var float
     */
    public $minPrice;
    /**
     * Minimal limit for stock
     *
     * @var int
     */
    public $minStock;

    /**
     * Get Targets
     *
     * @return string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    /**
     * PriceStockLess constructor.
     *
     * @param null $options
     */
    public function __construct($options = null)
    {
        if (null !== $options && !is_array($options)) {
            $options = array(
                'minPrice' => $options,
                'minStock' => $options,
            );
        }

        parent::__construct($options);

        if (null === $this->minStock && null === $this->minPrice) {
            throw new MissingOptionsException(
                sprintf(
                    'Options "minPrice", "minStock" must be given for constraint %s',
                    __CLASS__
                ),
                array('minPrice', 'minStock')
            );
        }
    }
}