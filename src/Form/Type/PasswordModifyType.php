<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class PasswordModifyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('old_password', PasswordType::class, [
                'label' => 'Contraseña actual'
            ])
            ->add('new_password', RepeatedType::class, [
                "type" => PasswordType::class,
                'invalid_message' => 'Las contraseñas no coinciden',
                'required' => true,
                'first_options' => ['label' => 'Nueva contraseña'],
                'second_options' => ['label' => 'Repetir contraseña']
            ])
            ->add("Enviar", SubmitType::class, [
                "label" => "Cambiar contraseña"
            ]);
    }
}