<?php

namespace Anton\ShopBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            ->add('description', TextType::class)
            ->add('isActive', ChoiceType::class, array(
                'choices' => [
                    'Yes' => true,
                    'No' => false,
                ],
            ))
            ->add('category', EntityType::class, array(
                'class' => 'Anton\ShopBundle\Entity\Category',
                'choice_label' => 'name',
                'empty_data' => null,
                'placeholder' => '----Choose parent----',
            ))
            ->add('picture', FileType::class, array('label' => 'Picture'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Anton\ShopBundle\Entity\Product',
        ));
    }

    public function getName()
    {
        return 'anton_shop_bundle_product_type';
    }
}
