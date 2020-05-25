<?php


namespace App\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class MensajeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("mensaje", TextareaType::class, [
                "label" => "Mensaje Privado"
            ])
            ->add("submit", SubmitType::class, [
                "label" => "Enviar"
            ])
            ->add("Cancelar", ResetType::class)
        ;
    }
}