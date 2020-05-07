<?php


namespace App\Controller;

use App\Entity\User;
use App\Form\Type\UserEditType;
use App\Form\Type\UserType;
use App\Service\PoliticumDataAccess;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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


    /**
     * @Route("/crear_usuario", name="crear_usuario")
     * @param PoliticumDataAccess $dataAccess
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     */
    public function crear_usuario(PoliticumDataAccess $dataAccess, Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $form = $this->createForm(UserType::class, new User());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $encodedPassword = $encoder->encodePassword(new User(), $user->getPassword());
            $user->setPassword($encodedPassword);

            if ($dataAccess->createUser($user)) {
                $this->addFlash("success", "El usuario se ha creado correctamete");
                return $this->redirectToRoute("index");
            } else {
                $this->addFlash("danger", "Hubo un error con la conexión a internet. Por favor, inténtalo de nuevo más tarde.");
            }
        }

        return $this->render('gestionar_usuario.twig', [
            'form' => $form->createView(),
            'crear' => true
        ]);
    }

    /**
     * @Route("/borrar_usuario", name="borrar_usuario")
     * @param PoliticumDataAccess $dataAccess
     * @return JsonResponse
     * @IsGranted("ROLE_ADMIN")
     */
    public function borrar_usuario(Request $request, PoliticumDataAccess $dataAccess): JsonResponse
    {
        if (!$request->request->has("id")) {
            throw new AccessDeniedException();
        }

        if ($dataAccess->deleteUser($request->request->get("id"))) {
            return new JsonResponse([
                'content' => $this->renderView('listado_usuarios_table.twig', [
                    "usuarios" => $dataAccess->getUsers(),
                ]),
            ]);
        } else {
            return new JsonResponse(['content' => null]);
        }
    }


    /**
     * @Route("/editar_usuario/{id}", name="editar_usuario")
     * @param int $id
     * @param Request $request
     * @param PoliticumDataAccess $dataAccess
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     */
    public function editar_usuario(int $id, Request $request, PoliticumDataAccess $dataAccess, UserPasswordEncoderInterface $encoder): Response
    {
        $form = $this->createForm(UserEditType::class, new User($dataAccess->getUser($id)));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $aux = $user->getPassword();
            if (isset($aux)){
                $encodedPassword = $encoder->encodePassword(new User(), $user->getPassword());
                $user->setPassword($encodedPassword);
            }


            if ($dataAccess->updateUser($user, $id)) {
                $this->addFlash("success", "El usuario ha sido actualizado correctamente");
                return $this->redirectToRoute("listar_usuarios");
            } else {
                $this->addFlash("danger", "Hubo un error con la conexión a internet. Por favor, inténtalo de nuevo más tarde.");
            }
        }

        return $this->render('gestionar_usuario.twig', [
            'form' => $form->createView(),
            'crear' => false
        ]);
    }
}