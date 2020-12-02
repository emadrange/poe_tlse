<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('category', EntityType::class, [
                'placeholder' => 'article.form.category.placeholder',
                'class' => Category::class,
                'choice_label' => 'name',
                'required' => false
            ])
            ->add('imageFile', VichFileType::class, [
                'required' => false,
            ])
            ->add('summary', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new Length([
                        'min' => 20,
                        'minMessage' => 'article.validator.summary.min_message'
                    ]),
                    new NotBlank([
                        'message' => 'article.validator.summary.blank_message'
                    ]),
                ],
                'attr' => [
                    'rows' => 5,
                    'cols' => 40,
                ],
            ])
            ->add('content', TextareaType::class, [
                'attr' => [
                    'rows' => 10,
                    'cols' => 40,
                ],
            ])
            ->add('published', CheckboxType::class, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'label_format' => 'article.form.%name%.label'
        ]);
    }
}
