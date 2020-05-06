<?php

namespace App\Service;

use App\Entity\User;

class PoliticumDataAccess extends DataAccess {
    public function getUsers()
    {
        return parent::indexRowByCod(parent::executeSQL("SELECT * FROM usuarios;"));
    }


    public function createUser(User $user)
    {
        return parent::executeSQL("INSERT INTO usuarios (id, user, password, surname, name, dni, rol, email) 
            VALUES (:id, :user, :password, :surname, :name, :dni, :rol, :email)",
            array(
                'id' => 0,
                'user' => $user->getUsername(),
                'password' => $user->getPassword(),
                'surname' => $user->getSurname(),
                'name' => $user->getName(),
                'dni' => $user->getDni(),
                'rol' => $user->getRole(),
                'email' => $user->getEmail()
            )
        );
    }

    public function deleteUser(int $id)
    {
        return parent::executeSQL("DELETE FROM usuarios WHERE id = :id", array('id' => $id));
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