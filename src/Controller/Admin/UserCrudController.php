<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('username'),
            TextField::new('email'),
            BooleanField::new('is_verified'),
            AssociationField::new('city')->formatValue(function ($value, $entity) {
                if($entity->getCity() != null) {
                    return $entity->getCity()->getName();
                } else {
                    return '';
                }
            })->hideOnForm(),
            AssociationField::new('state')->formatValue(function ($value, $entity) {
                if($entity->getState() != null) {
                    return $entity->getState()->getName();
                } else {
                    return '';
                }
            })->hideOnForm(),
            AssociationField::new('country')->formatValue(function ($value, $entity) {
                if($entity->getCountry() != null) {
                    return $entity->getCountry()->getName();
                } else {
                    return '';
                }
            })->hideOnForm(),
        ];
    }
}
