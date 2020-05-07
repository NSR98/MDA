<?php

namespace App\Controller;

use App\Service\PoliticumDataAccess;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 */
class ForumController extends AbstractController {
    /**
     * @Route("/foro", name="foro")
     * @param PoliticumDataAccess $dataAccess
     * @return Response
     */
    public function foro(PoliticumDataAccess $dataAccess){
        return $this->render("foro.twig",  [
            "publicaciones" => $dataAccess->getPublicaciones(),
            "usuarios" => $dataAccess->getUsers()
        ]);
    }

    /**
     * @Route("/foro/publicacion/{id}", name="ver_publicacion")
     * @param PoliticumDataAccess $dataAccess
     * @param int $id
     * @return Response
     */
    public function ver_publicacion(PoliticumDataAccess $dataAccess, int $id){
        if (!$dataAccess->getPublicacion($id)) {
            $this->addFlash("danger", "La publicación a la que estabas intentando acceder no existe.");
            return $this->redirectToRoute("index");
        }

        return $this->render("ver_publicacion.twig",  [
            "publicacion" => $dataAccess->getPublicacion($id),
            "respuestas" => $dataAccess->getRespuestas($id),
        ]);
    }
}