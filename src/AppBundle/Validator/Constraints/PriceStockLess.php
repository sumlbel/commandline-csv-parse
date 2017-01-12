<?php

namespace AppBundle\Validator\Constraints;


use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PriceStockLess extends Constraint
{
    public $message = 'If price less than "{{ minPrice }}" '.
    'stock can not be less than "{{ minStock }}"';

    public $minPrice;
    public $minStock;

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

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