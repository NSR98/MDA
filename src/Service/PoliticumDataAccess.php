<?php

namespace App\Service;

class PoliticumDataAccess extends DataAccess {
    public function getUsers()
    {
        return parent::indexRowByCod(parent::executeSQL("SELECT * FROM usuarios;"));
    }

    public function getPublicacion(int $id)
    {
        return parent::executeSQL("SELECT * FROM publicaciones WHERE id = :id", [
            "id" => $id
        ])->fetch();
    }

    public function getRespuestas(int $id)
    {
        return parent::executeSQL("SELECT * FROM respuestas WHERE id_publicacion = :id ORDER BY fecha DESC", [
            "id" => $id
        ])->fetchAll();
    }
}