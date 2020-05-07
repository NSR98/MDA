<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Publicacion {
    private $id;
    /**
     * @Assert\NotNull
     * @Assert\Length(max="140")
     */
    private $titulo;
    /**
     * @Assert\NotNull
     * @Assert\Length(max="1000")
     */
    private $descripcion;
    private $fecha;

    /**
     * Publicacion constructor.
     * @param array $s
     */
    public function __construct(array $s = null)
    {
        $this->id = $s["id"] ?? null;
        $this->titulo = $s["titulo"] ?? null;
        $this->descripcion = $s["descripcion"] ?? null;
        $this->fecha = $s["fecha"] ?? null;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * @param mixed $titulo
     */
    public function setTitulo($titulo): void
    {
        $this->titulo = $titulo;
    }

    /**
     * @return mixed
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * @param mixed $descripcion
     */
    public function setDescripcion($descripcion): void
    {
        $this->descripcion = $descripcion;
    }

    /**
     * @return mixed
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * @param mixed $fecha
     */
    public function setFecha($fecha): void
    {
        $this->fecha = $fecha;
    }


}