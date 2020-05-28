<?php


namespace App\Controller;


use App\Service\PoliticumDataAccess;
use Symfony\Component\HttpFoundation\Request;
use phpDocumentor\Reflection\Types\This;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @IsGranted("ROLE_USER")
 */
class MessagesController extends AbstractController
{
    /**
     * @Route("/mensajes_privados", name="mensajes_privados")
     * @param PoliticumDataAccess $dataAccess
     * @return Response
     */
    public function mensajes_privados(PoliticumDataAccess $dataAccess)
    {
        return $this->render("mensajes_privados.twig", [
            "usuarios" => $dataAccess->getUsuariosConConversacionesActivas($this->getUser()->getId()),
        ]);
    }

    /**
     * @Route("/conversacion/{idusername}", name="conversacion")
     * @param PoliticumDataAccess $dataAccess
     * @param int $idusername
     * @return Response
     */
    public function conversacion(PoliticumDataAccess $dataAccess, int $idusername)
    {
        return $this->render("conversacion.twig", [
            "mensajes" => $dataAccess->getHiloDeMensajesPrivados($this->getUser()->getId(), $idusername),
        ]);
    }

    /**
     * @Route("/borrar_mensajes", name="borrar_mensajes")
     * @param PoliticumDataAccess $dataAccess
     * @return JsonResponse
     */
    public function borrar_mensajes(Request $request, PoliticumDataAccess $dataAccess): JsonResponse
    {

        if (!$request->request->has("idusername")) {
            throw new AccessDeniedException();
        }

        if ($dataAccess->borrarHiloDeMensajesPrivados($request->request->get("idusername"), $this->getUser()->getId())) {
            return new JsonResponse([
                'content' => $this->render("mensajes_privados.twig", [
                    "usuarios" => $dataAccess->getUsuariosConConversacionesActivas($this->getUser()->getId()),
                ])
            ]);
        } else {
            return new JsonResponse(['content' => null]);
        }

    }
}