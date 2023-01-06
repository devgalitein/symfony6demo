<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
class UserFixtures extends Fixture 
{

    private UserPasswordHasherInterface $passwordHasher;
    public function __construct(UserPasswordHasherInterface $passwordHasher) {
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {

        $passwordHasher = $this->passwordHasher;
        // dd($passwordHasher);
        $user = new User();
        
        $email = 'admin';
        $user->setUsername($email);
        $password = 12345678;
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $password
        );
        $user->setPassword($hashedPassword);

        $email = 'admin@gmail.com';
        $user->setEmail($email);
        
        $roles = ['ROLE_ADMIN'];
        $user->setRoles($roles);

        $manager->persist($user);
        $manager->flush();
    }
}
