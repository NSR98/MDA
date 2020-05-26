<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Mensaje {
    private $id;
    private $id_emisor;
    private $id_receptor;

    /**
     * @Assert\NotNull
     * @Assert\Length(max="1000")
     */
    private $mensaje;
    private $fecha;

    /**
     * Publicacion constructor.
     * @param array $s
     */
    public function __construct(array $s = null)
    {
        $this->id = $s["id"] ?? null;
        $this->id_emisor = $s["id_emisor"] ?? null;
        $this->id_receptor = $s["id_receptor"] ?? null;
        $this->mensaje = $s["mensaje"] ?? null;
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
    public function setId( $id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getIdEmisor()
    {
        return $this->id_emisor;
    }

    /**
     * @param mixed $id_emisor
     */
    public function setIdEmisor($id_emisor)
    {
        $this->id_emisor = $id_emisor;
    }

    /**
     * @return mixed
     */
    public function getIdReceptor()
    {
        return $this->id_receptor;
    }

    /**
     * @param mixed $id_receptor
     */
    public function setIdReceptor( $id_receptor)
    {
        $this->id_receptor = $id_receptor;
    }

    /**
     * @return mixed
     */
    public function getMensaje()
    {
        return $this->mensaje;
    }

    /**
     * @param mixed $mensaje
     */
    public function setMensaje( $mensaje)
    {
        $this->mensaje = $mensaje;
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
    public function setFecha( $fecha)
    {
        $this->fecha = $fecha;
    }



}