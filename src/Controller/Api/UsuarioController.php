<?php

namespace App\Controller\Api;

use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api/v1", name: 'api_v1_usuario_')]
class UsuarioController extends AbstractController
{
    #[Route("/lista", methods: ['GET'], name: "lista")]
    public function lista(EntityManagerInterface $doctrine): JsonResponse
    {
        $doctrine = $doctrine->getRepository(Usuario::class);

        return new JsonResponse($doctrine->pegarTodos());
    }

    #[Route("/cadastra", methods: ["POST"], name: 'cadastra')]
    public function cadastrar(Request $request, EntityManagerInterface $doctrine): Response
    {
        $data = $request->request->all();

        if (empty($data)) {
            $data = $request->query->all();
        }

        if (empty($data['nome']) || empty($data['email'])) {
            return new JsonResponse(['error' => 'Nome e email são obrigatórios.'], 400);
        }

        $usuario = new Usuario;

        $usuario->setNome($data["nome"]);
        $usuario->setEmail($data["email"]);

        $doctrine->persist($usuario);
        $doctrine->flush();

        if( $doctrine->contains($usuario) )
        {
            return new Response("ok", 200);
        } else {
            return new Response("not found!", 404);
        }
    }
}
