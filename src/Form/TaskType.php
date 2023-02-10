<?php

namespace App\Form;


use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('title', TextType::class, [
            'label' => 'Titre',
            'attr' => [
                'placeholder' => 'Titre',
            ],
        ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'attr' => [
                    'placeholder' => 'Contenu',
                ],
            ])
            ->add('isDone', ChoiceType::class, [
                'label' => "Status de la tache",
                'choices'  => [
                    'Tache terminé' => true,
                    'Tache non terminé' => false,
                ],
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'disabled' => true,
                'choice_label' => 'username',
                'label' => 'Propriétaire',
                'attr' => [
                    'placeholder' => 'Propriétaire',
                ],

            ])->add('createdAt', DateType::class, [
                'disabled' => true,
                'label' => 'Date de création',
                'widget' => 'single_text',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
