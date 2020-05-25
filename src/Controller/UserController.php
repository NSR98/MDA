<?php


namespace App\Controller;

use App\Entity\ChangePassword;
use App\Entity\User;
use App\Form\Type\PasswordModifyType;
use App\Form\Type\RegisterUserType;
use App\Form\Type\UserEditType;
use App\Form\Type\UserModifyType;
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
     * @Route("/modificar_perfil/{id}", name="modificar_perfil")
     * @param int $id
     * @param Request $request
     * @param PoliticumDataAccess $dataAccess
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function modificar_perfil(int $id, Request $request, PoliticumDataAccess $dataAccess, UserPasswordEncoderInterface $encoder): Response
    {
        $user_actual = new User($dataAccess->getUser($id));
        $id_actual = $user_actual->getId();
        $email_actual = $user_actual->getEmail();
        $dni_actual = $user_actual->getDni();
        $username_actual = $user_actual->getUsername();
        $user_actual->setPassword(' ');
        $form = $this->createForm(UserModifyType::class, $user_actual);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $usuarios = $dataAccess->getUsers();
            if (!$this->username_is_valid($usuarios, $user) && $user->getUsername() != $username_actual) {
                $mensaje = "Hubo un error. El nombre de usuario ya existe, por favor, introduce otro.";
                return $this->error_formulario_modificar_perfil($form, $mensaje);
            }

            if (!$this->email_is_valid($usuarios, $user) && $user->getEmail() != $email_actual) {
                $mensaje = "Hubo un error. El email ya existe, por favor, introduce otro.";
                return $this->error_formulario_modificar_perfil($form, $mensaje);
            }

            if (!$this->dni_is_valid($usuarios, $user) && $user->getDni() != $dni_actual) {
                $mensaje = "Hubo un error. El DNI introducido ya está registrado.";
                return $this->error_formulario_modificar_perfil($form, $mensaje);
            }

            if ($dataAccess->modifyUser($user, $id)) {
                $this->addFlash("success", "El usuario ha sido actualizado correctamente");
                return $this->redirectToRoute("ver_usuario", [ 'id' => $id ]);
            } else {
                $this->addFlash("danger", "Hubo un error con la conexión a internet. Por favor, inténtalo de nuevo más tarde.");
            }
        }

        return $this->render('modificar_perfil.twig', [
            'form' => $form->createView(),
            'usuario' => $dataAccess->getUser($id)
        ]);
    }


    /**
     * @Route("/cambiar_contraseña/{id}", name="cambiar_contraseña")
     * @param int $id
     * @param Request $request
     * @param PoliticumDataAccess $dataAccess
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function cambiar_contraseña(int $id, Request $request, PoliticumDataAccess $dataAccess, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User($dataAccess->getUser($id));
        $old_password = new ChangePassword();
        $form = $this->createForm(PasswordModifyType::class, $old_password);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $change = $form->getData();
            $encodedNewPassword = $encoder->encodePassword(new User(), $change->getNewPassword());
            $change->setNewPassword($encodedNewPassword);

            if ($encoder->isPasswordValid($user, $change->getOldPassword())) {
                if ($dataAccess->modifyUserPassword($change, $id)) {
                    $this->addFlash("success", "La contraseña se ha actualizado correctamente. Debe volver a iniciar sesión.");
                    return $this->redirectToRoute("ver_usuario", [ 'id' => $id ]);
                } else {
                    $this->addFlash("danger", "Hubo un error con la conexión a internet. Por favor, inténtalo de nuevo más tarde.");
                }
            } else {
                $this->addFlash("danger", "La contraseña actual no es correcta.");
            }

        }

        return $this->render('cambiar_contraseña.twig', [
            'form' => $form->createView(),
            'usuario' => $dataAccess->getUser($id)
        ]);
    }


    /**
     * @Route("/ver_usuario/{id}", name="ver_usuario")
     * @param PoliticumDataAccess $dataAccess
     * @param Request $request
     * @param int $id
     * @return Response
     * @IsGranted("ROLE_USER")
     */
    public function ver_usuario(PoliticumDataAccess $dataAccess, Request $request, int $id){
        return $this->render('ver_usuario.twig', [
            'usuario' => $dataAccess->getUser($id)
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
     * @Route("/buscar_usuario", name="buscar_usuario")
     * @IsGranted("ROLE_USER")
     * @param PoliticumDataAccess $dataAccess
     * @return Response
     */
    public function buscar_usuario(PoliticumDataAccess $dataAccess): Response
    {
        $username_target = $_GET["username"];
        if ($username_target != null && !ctype_space($username_target)) {

            //Redireccion a vista provisional, en la próxima sesión de trabajo se creará la vista asociada a este método.
            return $this->render('busqueda_usuarios.twig', [
                'usuarios' => $dataAccess->getUserByUsername($username_target),
                'username_target' => $username_target
            ]);
        } else{
            $this->addFlash("danger", "Los términos de búsqueda introducidos tienen un formato incorrecto");
            return $this->redirectToRoute("index");
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

    /**
     * @Route("/reportar_usuario", name="reportar_usuario")
     * @IsGranted("ROLE_USER")
     * @param PoliticumDataAccess $dataAccess
     * @param Request $request
     * @return JsonResponse
     */
    public function reportar_usuario(PoliticumDataAccess $dataAccess, Request $request)
    {
        if (!$request->request->has("id_emisor") ||
            !$request->request->has("id_reportado") ||
            !$request->request->has("motivo")) {
            throw new AccessDeniedException();
        }

        if ($dataAccess->reportarUsuario($request->request->get("id_emisor"),
            $request->request->get("id_reportado"),
            $request->request->get("motivo"))) {
            $this->addFlash("success", "Tu reporte se ha almacenado correctamente. Gracias por mejorar Politicum");
            return new JsonResponse(["success" => true]);
        } else {
            return new JsonResponse(["success" => false]);
        }
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
            'form' => $form->createView()
        ]);
    }

    public function error_formulario_modificar_perfil($form, String $mensaje)
    {
        $this->addFlash("danger", $mensaje);
        return $this->render('modificar_perfil.twig', [
            'form' => $form->createView(),
            'usuario' => $this->getUser()
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