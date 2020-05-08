<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Respuesta
{
    private $id;
    private $id_usuario;
    private $id_publicacion;
    /**
     * @Assert\NotNull
     * @Assert\Length(max="1000")
     */
    private $respuesta;
    private $fecha;

    /**
     * Respuesta constructor.
     * @param array $a
     */
    public function __construct(array $a = null)
    {
        $this->id = $a["id"] ?? null;
        $this->id_usuario = $a["id_usuario"] ?? null;
        $this->id_publicacion = $a["id_publicacion"] ?? null;
        $this->respuesta = $a["respuesta"] ?? null;
        $this->fecha = $a["fecha"] ?? null;
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
    public function getIdUsuario()
    {
        return $this->id_usuario;
    }

    /**
     * @param mixed $id_usuario
     */
    public function setIdUsuario($id_usuario): void
    {
        $this->id_usuario = $id_usuario;
    }

    /**
     * @return mixed
     */
    public function getIdPublicacion()
    {
        return $this->id_publicacion;
    }

    /**
     * @param mixed $id_publicacion
     */
    public function setIdPublicacion($id_publicacion): void
    {
        $this->id_publicacion = $id_publicacion;
    }

    /**
     * @return mixed
     */
    public function getRespuesta()
    {
        return $this->respuesta;
    }

    /**
     * @param mixed $respuesta
     */
    public function setRespuesta($respuesta): void
    {
        $this->respuesta = $respuesta;
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
