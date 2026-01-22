<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudo',
                'attr' => ['placeholder' => 'Votre pseudo'],
                'constraints' => [
                    new NotBlank(['message' => 'Le pseudo est obligatoire']),
                    new Length([
                        'min' => 3,
                        'max' => 50,
                    ]),
                ],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'required' => false,
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'required' => false,
            ])
            ->add('age', IntegerType::class, [
                'label' => 'Âge',
                'required' => false,
                'constraints' => [
                    new Range([
                        'min' => 13,
                        'max' => 120,
                    ]),
                ],
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
                'required' => false,
            ])
            ->add('ville', TextType::class, [
                'label' => 'Ville',
                'required' => false,
                'attr' => ['class' => 'ville-autocomplete'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
