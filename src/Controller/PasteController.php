<?php

namespace App\Controller;
use App\Entity\Paste;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class PasteController extends AbstractController
{
    #[Route('/paste', name: 'create_paste', methods:['GET','POST'])]
    public function createPaste(
        Request $request, 
        EntityManagerInterface $em
    ): Response
    {
        $data = json_decode($request->getContent(), true);

        // Проверка на наличие необходимых полей
        if (!isset($data['title']) || !isset($data['content'])) {
            return new Response('Title and content are required', Response::HTTP_BAD_REQUEST);
        }

        $paste = new Paste();
        $paste->setTitle($data['title']);
        $paste->setContent($data['content']);
        $paste->setExpirationTime(new \DateTime("+1 hour"));
        $paste->setAccessLevel("public");

        $em->persist($paste);
        $em->flush();

        return new Response('Paste created.', Response::HTTP_CREATED);
    }
}
