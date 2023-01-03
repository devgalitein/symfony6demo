<?php

namespace App\Service;

use App\Helper\ValidationMessageHelper;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Validator\ProductUnique;
class ProductValidator
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateData(array $requestParams): array
    {
        $validate_arr = [
            'name' =>  [
                new Assert\NotBlank(message: 'name should not be blank'),
                new ProductUnique(),
            ],
            'price' =>  [
                new Assert\NotBlank(message: 'price should not be blank'),
            ],
        ];

        $constraint = new Assert\Collection($validate_arr);

        $validationResult = $this->validator->validate([
            'name' => $requestParams['name'],
            'price' => $requestParams['price'],
        ], $constraint);

        return ValidationMessageHelper::prepareMessage($validationResult);
    }
}