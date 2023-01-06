<?php

namespace App\Service;

use App\Helper\ValidationMessageHelper;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\File;
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
        if($requestParams['edit_id'] != null) {
            $validate_arr = [
                'name' =>  [
                    new Assert\NotBlank(message: 'name should not be blank'),
                    new ProductUnique(),
                ],
                'price' =>  [
                    new Assert\NotBlank(message: 'price should not be blank'),
                ],
                'product_image' =>  [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/*',
                        ],
                        'mimeTypesMessage' => 'This file is not a valid image.'
                    ]),
                ],
            ];
        } else {
            $validate_arr = [
                'name' =>  [
                    new Assert\NotBlank(message: 'name should not be blank'),
                    new ProductUnique(),
                ],
                'price' =>  [
                    new Assert\NotBlank(message: 'price should not be blank'),
                ],
                'product_image' =>  [
                    new Assert\NotBlank(message: 'The product image is required.'),
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/*',
                        ],
                        'mimeTypesMessage' => 'This file is not a valid image.'
                    ]),
                ],
            ];
        }
        

        $constraint = new Assert\Collection($validate_arr);

        $validationResult = $this->validator->validate([
            'name' => $requestParams['name'],
            'price' => $requestParams['price'],
            'product_image' => $requestParams['product_image'],
        ], $constraint);

        return ValidationMessageHelper::prepareMessage($validationResult);
    }
}