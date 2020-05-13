<?php


namespace App\Controller;

use App\Entity\User;
use App\Form\Type\RegisterUserType;
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

            $usuarios = $dataAccess->getUsers();
            if (!$this->username_is_valid($usuarios, $user)) {
                $mensaje = "Hubo un error. El nombre de usuario ya existe, por favor, introduce otro.";
                return $this->error_formulario($form, $mensaje);
            }

            if (!$this->email_is_valid($usuarios, $user)) {
                $mensaje = "Hubo un error. El email ya existe, por favor, introduce otro.";
                return $this->error_formulario($form, $mensaje);
            }

            if (!$this->dni_is_valid($usuarios, $user)) {
                $mensaje = "Hubo un error. El DNI introducido ya está registrado.";
                return $this->error_formulario($form, $mensaje);
            }

            if ($dataAccess->createUser($user)) {
                $this->addFlash("success", "El usuario se ha creado correctamente");
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
     * @Route("/registrar_usuario", name="registrar_usuario")
     * @param PoliticumDataAccess $dataAccess
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function registrar_usuario(PoliticumDataAccess $dataAccess, Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $form = $this->createForm(RegisterUserType::class, new User());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $encodedPassword = $encoder->encodePassword(new User(), $user->getPassword());
            $user->setPassword($encodedPassword);

            $usuarios = $dataAccess->getUsers();
            if (!$this->username_is_valid($usuarios, $user)) {
                $mensaje = "Hubo un error. El nombre de usuario ya existe, por favor, introduce otro.";
                return $this->error_formulario_registro($form, $mensaje);
            }

            if (!$this->email_is_valid($usuarios, $user)) {
                $mensaje = "Hubo un error. El email ya existe, por favor, introduce otro.";
                return $this->error_formulario_registro($form, $mensaje);
            }

            if (!$this->dni_is_valid($usuarios, $user)) {
                $mensaje = "Hubo un error. El DNI introducido ya está registrado.";
                return $this->error_formulario_registro($form, $mensaje);
            }

            if ($dataAccess->registerUser($user)) {
                $this->addFlash("success", "Cuenta creada correctamente. Puedes iniciar sesión.");
                return $this->redirectToRoute("index");
            } else {
                $this->addFlash("danger", "Hubo un error con la conexión a internet. Por favor, inténtalo de nuevo más tarde.");
            }
        }

        return $this->render('registrar_usuario.twig', [
            'form' => $form->createView()
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
        $user_actual = new User($dataAccess->getUser($id));
        $id_actual = $user_actual->getId();
        $email_actual = $user_actual->getEmail();
        $dni_actual = $user_actual->getDni();
        $form = $this->createForm(UserEditType::class, $user_actual);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $aux = $user->getPassword();
            if (isset($aux)){
                $encodedPassword = $encoder->encodePassword(new User(), $user->getPassword());
                $user->setPassword($encodedPassword);
            }

            $usuarios = $dataAccess->getUsers();
            if (!$this->username_is_valid($usuarios, $user) && $user->getId() != $id_actual) {
                $mensaje = "Hubo un error. El nombre de usuario ya existe, por favor, introduce otro.";
                return $this->error_formulario($form, $mensaje);
            }

            if (!$this->email_is_valid($usuarios, $user) && $user->getEmail() != $email_actual) {
                $mensaje = "Hubo un error. El email ya existe, por favor, introduce otro.";
                return $this->error_formulario($form, $mensaje);
            }

            if (!$this->dni_is_valid($usuarios, $user) && $user->getDni() != $dni_actual) {
                $mensaje = "Hubo un error. El DNI introducido ya está registrado.";
                return $this->error_formulario($form, $mensaje);
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


    public function error_formulario($form, String $mensaje)
    {
        $this->addFlash("danger", $mensaje);
        return $this->render('gestionar_usuario.twig', [
            'form' => $form->createView(),
            'crear' => true
        ]);
    }

    public function error_formulario_registro($form, String $mensaje)
    {
        $this->addFlash("danger", $mensaje);
        return $this->render('registrar_usuario.twig', [
            'form' => $form->createView(),
            'crear' => true
        ]);
    }

    public function username_is_valid(array $usuarios, User $user)
    {
        foreach ($usuarios as $us) {
            if ($us["user"] == $user->getUsername()) {
                return false;
            }
        }
        return true;
    }

    public function email_is_valid(array $usuarios, User $user)
    {
        foreach ($usuarios as $us) {
            if ($us["email"] == $user->getEmail()) {
                return false;
            }
        }
        return true;
    }

    public function dni_is_valid(array $usuarios, User $user)
    {
        foreach ($usuarios as $us) {
            if ($us["dni"] == $user->getDni()) {
                return false;
            }
        }
        return true;
    }
}