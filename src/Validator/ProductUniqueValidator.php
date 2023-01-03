<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\Request;

class ProductUniqueValidator extends ConstraintValidator
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    
    public function validate($value, Constraint $constraint)
    {
        /* @var App\Validator\ProductUnique $constraint */
        
        $product_id = $_REQUEST['edit_id'] ?? null;
        if (null === $value || '' === $value) {
            return;
        }
        
        $product = $this->em->getRepository(Product::class)->isUniqueProductName($value,$product_id);
        if(empty($product)) {
            return;
        }

        // TODO: implement the validation here
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}
