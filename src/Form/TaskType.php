<?php

namespace App\Form;


use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\SecurityBundle\Security;

class TaskType extends AbstractType
{
    public function __construct(private Security $security)
    {
        $this->security = $security;
    }

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
            ->add('user', TextType::class, [
                'label' => 'Propriétaire',
                'disabled' => true,
                'attr' => [
                    'placeholder' => $this->security->getToken()->getUser()->getUsername(),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
