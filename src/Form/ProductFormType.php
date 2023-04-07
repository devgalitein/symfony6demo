<?php

namespace App\Form;

use App\Entity\ProductVariation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use App\Form\ProductVariantType;
use App\Entity\Product;
class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('product_variations', CollectionType::class, [
            'entry_type' => ProductVariantType::class,
            'entry_options' => ['label' => false],
            'allow_add' => true,
            'label' => false,
            'allow_delete' => true,
            'by_reference' => false,
        ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
