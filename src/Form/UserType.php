<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname')
            ->add('lastname')
            ->add('author', AuthorType::class, [
                'label' => false,
            ])
            ->add('friend', CollectionType::class, [
                'entry_type' => EntityType::class,
                'entry_options' => [
                    'class' => User::class,
                    'choice_label' => 'lastname',
                ],
                'allow_add' => true,
            ])
            /*->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'lastname',
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
