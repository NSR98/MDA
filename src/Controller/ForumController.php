<?php

namespace App\Controller;

use App\Entity\Publicacion;
use App\Entity\Respuesta;
use App\Entity\Mensaje;
use App\Form\Type\PublicacionType;
use App\Form\Type\RespuestaEditType;
use App\Form\Type\RespuestaType;
use App\Form\Type\MensajeType;
use App\Service\PoliticumDataAccess;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
     * @Route("/foro/crear_publicacion", name="crear_publicacion")
     * @param PoliticumDataAccess $dataAccess
     * @param Request $request
     * @return Response
     */
    public function crear_publicacion(PoliticumDataAccess $dataAccess, Request $request){
        $form = $this->createForm(PublicacionType::class, new Publicacion());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$dataAccess->createPublicacion($form->getData(), $this->getUser()->getId())) {
                $this->addFlash("success", "La publicación se ha creado correctamete");
                return $this->redirectToRoute("foro");
            } else {
                $this->addFlash("danger", "Hubo un error con la conexión a internet. Por favor, inténtalo de nuevo más tarde.");
            }
        }

        return $this->render('gestionar_publicacion.twig', [
            'form' => $form->createView(),
            'operacion' => "Crear"
        ]);
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
        if (!$dataAccess->getPublicacion($id)) {
            $this->addFlash("danger", "La publicación a la que estabas intentando acceder no existe.");
            return $this->redirectToRoute("index");
        }
        $form = $this->createForm(PublicacionType::class, new Publicacion($dataAccess->getPublicacion($id)));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$dataAccess->updatePublicacion($form->getData(), $id)) {
                $this->addFlash("success", "Publicación modificada correctamente");
                return $this->redirectToRoute("foro");
            } else {
                $this->addFlash("danger", "Hubo un error con la conexión a internet. Por favor, inténtalo de nuevo más tarde.");
            }
        }

        return $this->render('gestionar_publicacion.twig', [
            'form' => $form->createView(),
            'operacion' => "Modificar"
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
        if (!$request->request->has("id") ||
            !$dataAccess->getPublicacion($request->request->get("id")) ||
            ($dataAccess->getPublicacion($request->request->get("id"))["id_usuario"] != $this->getUser()->getId() &&
                !$this->isGranted("ROLE_ADMIN"))) {
            throw new AccessDeniedException();
        }

        if (!$dataAccess->deletePublicacion($request->request->get("id"))) {
            return new JsonResponse([
                'content' => $this->renderView('foro_table.twig', [
                    "publicaciones" => $dataAccess->getPublicaciones(),
                    "usuarios" => $dataAccess->getUsers()
                ]),
            ]);
        } else {
            return new JsonResponse(['content' => null]);
        }
    }



    /**
     * @Route("/foro/publicacion/{id}/crear_respuesta", name="crear_respuesta")
     * @param PoliticumDataAccess $dataAccess
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function crear_respuesta(PoliticumDataAccess $dataAccess, Request $request, int $id){
        $form = $this->createForm(RespuestaType::class, new Respuesta());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$dataAccess->createRespuesta($form->getData(), $this->getUser()->getId(), $id)) {
                $this->addFlash("success", "La respuesta se ha creado correctamete");
                return $this->redirectToRoute("ver_publicacion", array('id' => $id));
            } else {
                $this->addFlash("danger", "Hubo un error con la conexión a internet. Por favor, inténtalo de nuevo más tarde.");
            }
        }

        return $this->render('gestionar_respuesta.twig', [
            'form' => $form->createView(),
            'crear' => true
        ]);
    }

    /**
     * @Route("/borrar_respuesta", name="borrar_respuesta")
     * @param Request $request
     * @param PoliticumDataAccess $dataAccess
     * @return JsonResponse
     */
    public function borrar_respuesta(Request $request, PoliticumDataAccess $dataAccess): JsonResponse
    {
        dump($request->request->get("id"));
        if (!$request->request->has("id") ||
            !$dataAccess->getRespuesta($request->request->get("id")) ||
            ($dataAccess->getRespuesta($request->request->get("id"))["id_usuario"] != $this->getUser()->getId() &&
                !$this->isGranted("ROLE_ADMIN"))) {
            throw new AccessDeniedException();
        }
        $id_publicacion = $dataAccess->getRespuesta($request->request->get("id"))["id_publicacion"];
        if (!$dataAccess->deleteRespuesta($request->request->get("id"))) {
            return new JsonResponse([
                'content' => $this->renderView('ver_publicacion.twig', [
                    "publicacion" => $dataAccess->getPublicacion($id_publicacion),
                    "respuestas" => $dataAccess->getRespuestas($request->request->get("id")),
                    "usuarios" => $dataAccess->getUsers()
                ]),
            ]);
        } else {
            return new JsonResponse(['content' => null]);
        }
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

    /**
     * @Route("/foro/editar_respuesta/{id}", name="editar_respuesta")
     * @param PoliticumDataAccess $dataAccess
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function editar_respuesta(PoliticumDataAccess $dataAccess, Request $request, int $id){
        if (!$dataAccess->getRespuesta($id)) {
            $this->addFlash("danger", "La publicación a la que estabas intentando acceder no existe.");
            return $this->redirectToRoute("index");
        }
        $form = $this->createForm(RespuestaEditType::class, new Respuesta($dataAccess->getRespuesta($id)));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$dataAccess->updateRespuesta($form->getData(), $id)) {
                $this->addFlash("success", "Respuesta modificada correctamente");
                $respuesta =  new Respuesta($dataAccess -> getRespuesta($id));
                $idp = $respuesta -> getIdPublicacion();
                return $this->redirectToRoute("ver_publicacion", ["id" => $idp]);
            } else {
                $this->addFlash("danger", "Hubo un error con la conexión a internet. Por favor, inténtalo de nuevo más tarde.");
            }
        }

        return $this->render('gestionar_respuesta.twig', [
            'form' => $form->createView(),
            'crear' => false
        ]);
    }

    /**
     * @Route("/foro/ver_usuario/{id}/crear_mensaje", name="crear_mensaje")
     * @param PoliticumDataAccess $dataAccess
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function crear_mensaje(PoliticumDataAccess $dataAccess, Request $request, int $id){
        $form = $this->createForm(MensajeType::class, new Mensaje());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$dataAccess->createMensaje($form->getData(), $this->getUser()->getId(), $id)) {
                $this->addFlash("success", "Mensaje enviado correctamete");
                return $this->redirectToRoute("mensajes_privados", array('id' => $id));
            } else {
                $this->addFlash("danger", "Hubo un error con la conexión a internet. Por favor, inténtalo de nuevo más tarde.");
            }
        }

        return $this->render('gestionar_mensaje.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/foro/ver_usuario/{id}/crear_mensaje_prueba", name="crear_mensaje_prueba")
     * @param PoliticumDataAccess $dataAccess
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function crear_mensaje_prueba(PoliticumDataAccess $dataAccess, Request $request, int $id){
        $form = $this->createForm(MensajeType::class, new Mensaje());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$dataAccess->createMensaje($form->getData(), $this->getUser()->getId(), $id)) {
                $this->addFlash("success", "Mensaje enviado correctamete");
                return $this->redirectToRoute("conversacion", array('idusername' => $id));
            } else {
                $this->addFlash("danger", "Hubo un error con la conexión a internet. Por favor, inténtalo de nuevo más tarde.");
            }
        }

        return $this->render('gestionar_mensaje.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/foro/busqueda_avanzada", name="buscar_publicacion")
     * @IsGranted("ROLE_USER")
     * @param PoliticumDataAccess $dataAccess
     * @param Request $request
     * @return Response
     */
    public function buscar_publicacion(PoliticumDataAccess $dataAccess, Request $request){
        if ($request->request->has("search") && $request->request->has("type")) {
            if (empty($request->request->get("search"))) {
                $publicaciones = $dataAccess->getPublicaciones();
            } else {
                switch ($request->request->get("type")) {
                    case "title" :
                        $publicaciones = $dataAccess->searchPublicacionesByTitle($request->request->get("search"));
                        break;
                    case "description" :
                        $publicaciones = $dataAccess->searchPublicacionesByDescription($request->request->get("search"));
                        break;
                    case "author" :
                        $publicaciones = $dataAccess->searchPublicacionesByAuthor($request->request->get("search"));
                        break;
                    case "realName" :
                        $publicaciones = $dataAccess->searchPublicacionesByRealName($request->request->get("search"));
                        break;
                    default :
                        throw new AccessDeniedException();
                }
            }

            return new JsonResponse([
                'content' => $this->renderView('foro_table.twig', [
                    "publicaciones" => $publicaciones,
                    "usuarios" => $dataAccess->getUsers()
                ]),
                'number_of_results' => count($publicaciones),
            ]);
        }
        return $this->render("buscar_publicacion.twig");
    }
}