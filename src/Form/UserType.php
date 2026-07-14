<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isNew = $options['is_new'];

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nome',
                'required' => false,
                'attr' => ['placeholder' => 'Nome completo', 'autocomplete' => 'name'],
            ])
            ->add('username', TextType::class, [
                'label' => 'Username',
                'attr' => ['placeholder' => 'Ex: joaosilva', 'autocomplete' => 'username'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail',
                'attr' => ['placeholder' => 'email@exemplo.com', 'autocomplete' => 'email'],
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Perfis de Acesso',
                'choices' => [
                    'Administrador' => 'ROLE_ADMIN',
                    'Usuário' => 'ROLE_USER',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'required' => $isNew,
                'first_options' => [
                    'label' => 'Senha',
                    'attr' => ['placeholder' => 'Mínimo 8 caracteres', 'autocomplete' => 'new-password'],
                ],
                'second_options' => [
                    'label' => 'Confirmar Senha',
                    'attr' => ['placeholder' => 'Repita a senha', 'autocomplete' => 'new-password'],
                ],
                'invalid_message' => 'As senhas não conferem.',
                'constraints' => $isNew ? [
                    new NotBlank(['message' => 'Informe uma senha.']),
                    new Length(min: 8, minMessage: 'A senha deve ter pelo menos {{ limit }} caracteres.'),
                ] : [
                    new Length(min: 8, minMessage: 'A senha deve ter pelo menos {{ limit }} caracteres.'),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_new' => false,
        ]);

        $resolver->setAllowedTypes('is_new', 'bool');
    }
}
