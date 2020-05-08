<?php


namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class PublicacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("titulo", TextType::class, [
                "label" => "Título"
            ])
            ->add("descripcion", TextareaType::class, [
                "label" => "Descripción"
            ])
            ->add("submit", SubmitType::class, [
                "label" => "Crear"
            ]);
    }
}
