<?php

namespace App\Service;

use App\Entity\Publicacion;
use App\Entity\Respuesta;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PoliticumDataAccess extends DataAccess {
    public function getUsers()
    {
        return parent::indexRowByCod(parent::executeSQL("SELECT * FROM usuarios;"));
    }

    public function getUser($id)
    {
        return parent::executeSQL("SELECT * FROM usuarios WHERE id = :id;", [
            "id" => $id
        ])->fetch();
    }

    public function getUserByUsername($username)
    {
        return parent::indexRowByCod(parent::executeSQL("SELECT * FROM usuarios WHERE user = :username;" , [
            "username" => $username
        ]));
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

    public function registerUser(User $user)
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
                'rol' => 1,
                'email' => $user->getEmail()
            )
        );
    }

    public function deleteUser(int $id)
    {
        return parent::executeSQL("DELETE FROM usuarios WHERE id = :id", array('id' => $id));
    }

    public function updateUser(User $user, $id)
    {
        return parent::executeSQL("UPDATE usuarios SET user= :user, password= :password, surname= :surname, name = :name, 
                                        dni= :dni, rol = :rol, email = :email WHERE id= :id",
            [
                'user' => $user->getUsername(),
                'password' => $user->getPassword(),
                'surname' => $user->getSurname(),
                'name' => $user->getName(),
                'dni' => $user->getDni(),
                'rol' => $user->getRole(),
                'email' => $user->getEmail(),
                'id' => $id
            ]
        );
    }

    public function getPublicaciones()
    {
        return parent::executeSQL("SELECT * FROM publicaciones ORDER BY fecha DESC;")->fetchAll();
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

    public function getRespuesta(int $id)
    {
        return parent::executeSQL("SELECT * FROM respuestas WHERE id = :id", [
            "id" => $id
        ])->fetch();
    }

    public function createRespuesta(Respuesta $respuesta, int $id_usuario, int $id_publicacion)
    {
        parent::executeSQL("INSERT INTO respuestas(id_usuario, id_publicacion, respuesta, fecha) VALUES (:id_usuario, :id_publicacion, :respuesta, :fecha);", [
            "id_usuario" => $id_usuario,
            "id_publicacion" => $id_publicacion,
            "respuesta" => $respuesta->getRespuesta(),
            "fecha" => date("Y-m-d H:i:s")
        ]);
    }

    public function updatePublicacion(Publicacion $publicacion, int $id)
    {
        parent::executeSQL("UPDATE publicaciones SET titulo = :titulo, descripcion = :descripcion WHERE id = :id", [
            "titulo" => $publicacion->getTitulo(),
            "descripcion" => $publicacion->getDescripcion(),
            "id" => $id
        ]);
    }

    public function createPublicacion(Publicacion $publicacion, int $id_usuario)
    {
        parent::executeSQL("INSERT INTO publicaciones(titulo, descripcion, fecha, id_usuario) VALUES (:titulo, :descripcion, :fecha, :id_usuario);", [
            "titulo" => $publicacion->getTitulo(),
            "descripcion" => $publicacion->getDescripcion(),
            "fecha" => date("Y-m-d H:i:s"),
            "id_usuario" => $id_usuario
        ]);
    }

    /**
     * @Route("/ver_usuario/{id}", name="ver_usuario")
     * @param PoliticumDataAccess $dataAccess
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function ver_usuario(PoliticumDataAccess $dataAccess, Request $request, int $id){
        return $this->render('ver_usuario.twig', [
            'usuario' => $dataAccess->getUser($id)
        ]);
    }

    public function deletePublicacion(int $id)
    {
        parent::executeSQL("DELETE FROM publicaciones WHERE id = :id", ["id" => $id]);
    }

    public function deleteRespuesta(int $id)
    {
        parent::executeSQL("DELETE FROM respuestas WHERE id = :id", ["id" => $id]);
    }
}