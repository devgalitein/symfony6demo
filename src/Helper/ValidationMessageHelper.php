<?php

namespace App\Helper;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationMessageHelper
{
    /**
     * @param ConstraintViolationListInterface $validationResult
     *
     * @return array<int|string, (mixed)>
     */
    public static function prepareMessage(ConstraintViolationListInterface $validationResult): array
    {
        $errorMessages = [];
        foreach ($validationResult as $validation) {
            $getName = $validation->getPropertyPath();
            $getName = preg_replace("/[^a-zA-Z0-9]+/", "", $getName);
            $errorMessages[$getName] = $validation->getMessage();
        }

        return $errorMessages;
    }
}
