<?php

namespace Anton\ShopBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Anton\ShopBundle\Entity\Category;

class CategoryType extends AbstractType
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
                'choices' => $options['parents'],
                'choice_label' => 'name',
                'empty_data' => null,
                'placeholder' => 'root',
                'required' => false,
            ));

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $product = $event->getData();
            $form = $event->getForm();

            if (!$product || null === $product->getId()) {
                $form->remove('parent');
            }
        });

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Anton\ShopBundle\Entity\Category',
            'parents' => [],
        ));
    }

    public function getName()
    {
        return 'anton_shop_bundle_edit_category';
    }
}
