<?php


namespace App\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class RespuestaEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("respuesta", TextareaType::class, [
                "label" => "Respuesta"
            ])
            ->add("submit", SubmitType::class, [
                "label" => "Modificar"
            ])
            ->add("Cancelar", ResetType::class)
        ;
    }
}