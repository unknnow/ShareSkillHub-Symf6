<?php

namespace App\Form;

use App\Entity\Notation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NotationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('note', IntegerType::class, [
                'label' => 'Note',
                'attr' => [
                    'min' => 0,
                    'max' => 5,
                ],
                'help' => 'Minimum = 0 / Maximum = 5',
                'required' => true
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'Message',
                'attr' => [
                    'placeholder' => 'Superbe session !'
                ],
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Notation::class,
        ]);
    }
}
