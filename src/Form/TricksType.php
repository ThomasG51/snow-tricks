<?php

namespace App\Form;

use App\Entity\Tricks;
use App\Entity\Type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TricksType extends AbstractType
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
                    'class' => 'w-full',
                    'onchange' => 'showRangeValue(this.value);'
                ]
            ])
            ->add('content', TextareaType::class, [
                'attr' => [
                    'class' => 'tw-form-field',
                    'rows' => 14
                ]
            ])
            ->add('type', EntityType::class, [
                'class' => Type::class,
                'attr' => [
                    'class' => 'tw-form-field'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tricks::class,
        ]);
    }
}
