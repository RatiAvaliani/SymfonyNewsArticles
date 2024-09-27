<?php

namespace App\Form;

use App\Entity\Articles;
use App\Entity\Categories;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;use function Symfony\Component\String\u;

class ArticlesFromType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Title')
            ->add('insert_date', null, [
                'widget' => 'single_text'
            ])
            /*, ['extensions' => ['jpeg', 'jpg', 'png']]*/
            ->add('image', FileType::class, [
                'required' => false,
                'mapped' => false
            ])

            ->add('short_description')
            ->add('content')
            ->add('categories', EntityType::class, [
                'attr' => [
                    'class' => 'multiple-select2',
                ],
                'class' => Categories::class,
                'multiple' => true,
                'choice_label' => 'title',
                'choice_value' => function (Categories $category) : int {
                    return $category->getId();
                },

                // TODO: Add an id encoder
            ])
            ->add('Save', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Articles::class,
        ]);
    }
}
