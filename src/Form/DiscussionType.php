<?php

namespace App\Form;

use App\Entity\Discussion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Formulaire pour ajouter ou modifier une discussion
 * Max 5000 caractères selon le cahier des charges
 */
class DiscussionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contenu', TextareaType::class, [
                'label' => 'Votre message',
                'attr' => [
                    'placeholder' => 'Écrivez votre message ici...',
                    'rows' => 5,
                    'maxlength' => 5000, // limite côté HTML
                    'class' => 'discussion-textarea',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le message ne peut pas être vide']),
                    new Length([
                        'max' => 5000,
                        'maxMessage' => 'Le message ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Discussion::class,
        ]);
    }
}
