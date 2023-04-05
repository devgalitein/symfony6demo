<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class UserEmailUniqueValidator extends ConstraintValidator
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    
    public function validate($value, Constraint $constraint)
    {
        
        $user_id = $_REQUEST['edit_id'] ?? null;
        if (null === $value || '' === $value) {
            return;
        }
        
        $user = $this->em->getRepository(User::class)->isUniqueUserEmail($value,$user_id);
        if(empty($user)) {
            return;
        }

        // TODO: implement the validation here
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}
