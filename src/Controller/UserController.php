<?php


namespace App\Controller;

use App\Service\PoliticumDataAccess;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/listar_usuarios", name="listar_usuarios")
     * @IsGranted("ROLE_ADMIN")
     * @param PoliticumDataAccess $dataAccess
     * @return Response
     */
    public function listar_usuarios(PoliticumDataAccess $dataAccess): Response
    {
        return $this->render('listado_usuarios.twig', [
            'usuarios' => $dataAccess->getUsers()
        ]);
    }
}