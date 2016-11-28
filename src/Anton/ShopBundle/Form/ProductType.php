<?php

namespace Anton\ShopBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Anton\ShopBundle\Entity\Category;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('category', EntityType::class, [
                'class' => 'Anton\ShopBundle\Entity\Category',
                'choice_label' => 'name',
                'empty_data' => null,
                'placeholder' => '----Choose parent----',
            ])
            ->add('isActive', ChoiceType::class, [
                'choices' => [
                    'Yes' => 1,
                    'No' => 0,
                ],
                'label' => 'Active?',
            ])
            ->add('relatedProducts', EntityType::class, [
                'class' => 'Anton\ShopBundle\Entity\Product',
                'choice_label' => 'name',
                'empty_data' => null,
                'expanded' => true ,
                'multiple' => true ,
                'placeholder' => '----Choose related product----',
            ])
            ->add('picture', FileType::class, [
                'label' => 'Picture',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Anton\ShopBundle\Entity\Product',
        ]);
    }

    public function getName()
    {
        return 'anton_shop_bundle_product_type';
    }
}
