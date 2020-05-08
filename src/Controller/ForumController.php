<?php

namespace App\Controller;

use App\Entity\Publicacion;
use App\Form\Type\PublicacionType;
use App\Service\PoliticumDataAccess;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
            "usuarios" => $dataAccess->getUsers()
        ]);
    }

    /**
     * @Route("/borrar_publicacion", name="borrar_publicacion")
     * @param Request $request
     * @param PoliticumDataAccess $dataAccess
     * @return JsonResponse
     */
    public function borrar_publicacion(Request $request, PoliticumDataAccess $dataAccess): JsonResponse
    {
        $dataAccess->deletePublicacion($request->request->get("id"));
        return new JsonResponse();
    }


    /**
     * @Route("/foro/modificar_publicacion/{id}", name="modificar_publicacion")
     * @IsGranted("ROLE_ADMIN")
     * @param PoliticumDataAccess $dataAccess
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function modificar_publicacion(PoliticumDataAccess $dataAccess, Request $request, int $id){
        //A implementar en la proxima sesion de trabajo
    }
}