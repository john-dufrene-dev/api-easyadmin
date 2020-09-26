<?php

namespace App\Form\Admin\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'asserts.change_password.require',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'asserts.change_password.min_length',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                        new NotCompromisedPassword([
                            'message' => 'asserts.admin.password.not_compromise',
                        ]),
                    ],
                    'label' => false,
                    'row_attr' => [
                        'class' => 'form-group field-text'
                    ],
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'reset.new_password.placeholder',
                    ],
                ],
                'second_options' => [
                    'label' => false,
                    'row_attr' => [
                        'class' => 'form-group field-text'
                    ],
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'reset.repeat_password.placeholder',
                    ],
                ],
                'invalid_message' => 'asserts.change_password.match',
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'admin'
        ]);
    }
}
