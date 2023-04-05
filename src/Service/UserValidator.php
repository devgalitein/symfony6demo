<?php

namespace App\Service;

use App\Helper\ValidationMessageHelper;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Validator\UserEmailUnique;
use App\Validator\UsernameUnique;
class UserValidator
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
                'username' =>  [
                    new Assert\NotBlank(message: 'username should not be blank'),
                ],
                'email' =>  [
                    new Assert\NotBlank(message: 'email should not be blank'),
                    // new UserEmailUnique(),
                ],
                'password' =>  [
                    new Assert\NotBlank(message: 'password should not be blank'),
                ],
                'country' =>  [
                    new Assert\NotBlank(message: 'country should not be blank'),
                ],
                'state' =>  [
                    new Assert\NotBlank(message: 'state should not be blank'),
                ],
                'city' =>  [
                    new Assert\NotBlank(message: 'city should not be blank'),
                ]
            ];
        } else {
            $validate_arr = [
                'username' =>  [
                    new Assert\NotBlank(message: 'username should not be blank'),
                    new UsernameUnique(),
                ],
                'email' =>  [
                    new Assert\NotBlank(message: 'email should not be blank'),
                    new Assert\Email(message: 'The email is not a valid email.'),
                    new UserEmailUnique(),
                ],
                'password' =>  [
                    new Assert\Length(
                        min: 6,
                        max: 8,
                        minMessage: 'Your password must be at least {{ limit }} characters long',
                        maxMessage: 'Your password cannot be longer than {{ limit }} characters',
                    ),
                    new Assert\NotBlank(message: 'password should not be blank'),
                    
                ],
                'country' =>  [
                    new Assert\NotBlank(message: 'country should not be blank'),
                ],
                'state' =>  [
                    new Assert\NotBlank(message: 'state should not be blank'),
                ],
                'city' =>  [
                    new Assert\NotBlank(message: 'city should not be blank'),
                ]
            ];
        }
        

        $constraint = new Assert\Collection($validate_arr);

        $validationResult = $this->validator->validate([
            'username' => $requestParams['username'],
            'email' => $requestParams['email'],
            'password' => $requestParams['password'],
            'country' => $requestParams['country'],
            'state' => $requestParams['state'],
            'city' => $requestParams['city'],
        ], $constraint);

        return ValidationMessageHelper::prepareMessage($validationResult);
    }
}