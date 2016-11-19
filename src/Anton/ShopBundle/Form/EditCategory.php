<?php

namespace Anton\ShopBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Anton\ShopBundle\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EditCategory extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('isActive', ChoiceType::class, array(
                'choices' => [
                    'Yes' => true,
                    'No' => false,
                ],
            ))
            ->add('parent', EntityType::class, array(
                'class' => 'Anton\ShopBundle\Entity\Category',
                'choice_label' => function ($category) {
                    return $category->getName();
                },
                'empty_data' => null,
                'required' => true,

            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Anton\ShopBundle\Entity\Category',
        ));
    }

    public function getName()
    {
        return 'anton_shop_bundle_edit_category';
    }
}
