<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("username", TextType::class, [
                "label" => "Nombre de usuario"
            ])
            ->add("password", PasswordType::class, [
                "required" => false,
                "label" => "Contraseña"
            ])
            ->add("surname", TextType::class, [
                "label" => "Apellidos"
            ])
            ->add("name", TextType::class, [
                "label" => "Nombre"
            ])
            ->add("dni", TextType::class, [
                "label" => "DNI"
            ])
            ->add("email", EmailType::class, [
                "label" => "Correo electrónico"
            ])
            ->add("role", ChoiceType::class, [
                "choices" => [
                    "Usuario" => 1,
                    "Administrador" => 0
                ],
                "label" => "Rol"
            ])
            ->add("Enviar", SubmitType::class, [
                "label" => "Editar"
            ])
            ->add("Limpiar", ResetType::class)
        ;
    }
}
