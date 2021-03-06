<?php

namespace App\Form;

use App\Entity\Media;
use App\Entity\Trick;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\SluggerInterface;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'tw-form-field'
                ]
            ])
            ->add('difficulty', RangeType::class, [
                'attr' => [
                    'min' => 1,
                    'max' => 3,
                    'class' => 'w-full cursor-pointer',
                    'onchange' => 'showRangeValue(this.value);'
                ]
            ])
            ->add('media', CollectionType::class, [
                'entry_type' => MediaType::class,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'label' => false
            ])
            ->add('videos', CollectionType::class, [
                'entry_type' => VideoType::class,
                'entry_options' => [
                    'attr' => [
                        'class' => 'tw-form-field mt-3'
                    ],
                ],
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'label' => false
            ])
            ->add('content', TextareaType::class, [
                'attr' => [
                    'class' => 'tw-form-field',
                    'rows' => 14
                ]
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'tw-form-field'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
            'attr' => [
                'id' => 'trick_form'
            ]
        ]);
    }
}
