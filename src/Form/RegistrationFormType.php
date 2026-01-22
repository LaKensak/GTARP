<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class RegistrationFormType extends AbstractType
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // captcha
        $session = $this->requestStack->getSession();

        if (!$session->has('captcha_num1') || $options['regenerate_captcha']) {
            $num1 = rand(1, 10);
            $num2 = rand(1, 10);
            $session->set('captcha_num1', $num1);
            $session->set('captcha_num2', $num2);
        }

        $num1 = $session->get('captcha_num1');
        $num2 = $session->get('captcha_num2');

        $builder
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail *',
                'attr' => ['placeholder' => 'votre@email.com'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre email']),
                    new Email(['message' => 'Veuillez entrer un email valide']),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false, // map
                'first_options' => [
                    'label' => 'Mot de passe *',
                    'attr' => ['placeholder' => 'Votre mot de passe'],
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe *',
                    'attr' => ['placeholder' => 'Confirmez votre mot de passe'],
                ],
                'invalid_message' => 'Les mots de passe ne correspondent pas',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer un mot de passe']),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères',
                        'max' => 4096, // limit
                    ]),
                ],
            ])
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudo *',
                'attr' => ['placeholder' => 'Votre pseudo'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez choisir un pseudo']),
                    new Length([
                        'min' => 3,
                        'max' => 50,
                        'minMessage' => 'Le pseudo doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le pseudo ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'required' => false,
                'attr' => ['placeholder' => 'Votre nom (optionnel)'],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'required' => false,
                'attr' => ['placeholder' => 'Votre prénom (optionnel)'],
            ])
            ->add('age', IntegerType::class, [
                'label' => 'Âge',
                'required' => false,
                'attr' => ['placeholder' => 'Votre âge (optionnel)'],
                'constraints' => [
                    new Range([
                        'min' => 13,
                        'max' => 120,
                        'notInRangeMessage' => 'L\'âge doit être entre {{ min }} et {{ max }} ans',
                    ]),
                ],
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'attr' => ['placeholder' => 'Votre téléphone (optionnel)'],
            ])
            ->add('ville', TextType::class, [
                'label' => 'Ville',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Votre ville (optionnel)',
                    'class' => 'ville-autocomplete', // js
                ],
            ])
            ->add('captcha', IntegerType::class, [
                'label' => 'Combien font ' . $num1 . ' + ' . $num2 . ' ? *',
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Votre réponse',
                    'autocomplete' => 'off',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez répondre à la question']),
                    new Callback([
                        'callback' => function ($value, ExecutionContextInterface $context) {
                            $session = $this->requestStack->getSession();
                            $num1 = $session->get('captcha_num1');
                            $num2 = $session->get('captcha_num2');
                            $expected = $num1 + $num2;

                            if ((int) $value !== $expected) {
                                $context->buildViolation('Mauvaise réponse, veuillez réessayer')
                                    ->addViolation();
                            }
                        },
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'regenerate_captcha' => false,
        ]);
    }
}
