<?php


namespace App\Controller;


use App\Service\PoliticumDataAccess;
use phpDocumentor\Reflection\Types\This;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
            "usuario" => $dataAccess->getUser($idusername),
        ]);
    }
}