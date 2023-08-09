<?php

namespace App\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, ['label' => 'Ancien mot de passe'])
            ->add('newPassword', PasswordType::class, ['label' => 'Nouveau mot de passe'])
            ->add('newPasswordConfirmation', PasswordType::class, ['label' => 'Confirmer le nouveau mot de passe']);
    }
}
