<?php

namespace App\Form;

use App\Entity\Session;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SessionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('subject', TextType::class, [
                'label' => 'Sujet',
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                'choices' => [
                    'En ligne' => 'online',
                    'Sur site' => 'onSite',
                ],
                'required' => true,
            ])
            ->add('startTime', DateTimeType::class, [
                'label' => 'Date de début de session',
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d H:i:s'),
                ],
                'required' => true,
            ])
            ->add('endTime', DateTimeType::class, [
                'label' => 'Date de fin de session',
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d H:i:s'),
                ],
                'required' => true,
            ])
            ->add('participantUser', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'displayName',
                'multiple' => true,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Session::class,
        ]);
    }
}
