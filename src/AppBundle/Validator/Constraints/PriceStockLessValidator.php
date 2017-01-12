<?php
/**
 * Created by PhpStorm.
 * User: s2.gerasimovich
 * Date: 1/12/17
 * Time: 12:39 PM
 */

namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\Product;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PriceStockLessValidator extends ConstraintValidator
{
    public function validate($product, Constraint $constraint)
    {
        if ($product->getPrice() < $constraint->minPrice &&
            $product->getStock() < $constraint->minStock
        ) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ minPrice }}', $constraint->minPrice)
                ->setParameter('{{ minStock }}', $constraint->minStock)
                ->addViolation();
        }
    }

}