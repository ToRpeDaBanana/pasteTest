<?php

namespace App\Controller;
use App\Entity\Paste;
use App\Form\PasteForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PasteController extends AbstractController
{
    #[Route('/', name: 'create_paste', methods:['GET','POST'])]
    public function createPaste(
        Request $request, 
        EntityManagerInterface $entityManager,
        SessionInterface $sessionInterface,
    ): Response
    {
        $user = new Paste();
        $form = $this->createForm(PasteForm::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
            $entityManager->persist($paste);
            $entityManager->flush();

            return new Response('Paste created.', Response::HTTP_CREATED);
        }
        $loggedIn = $sessionInterface->get('logged_in');
        $userId = $sessionInterface->get('user_id');
        

        return $this->render('paste/Paste.html.twig', [
            'form' => $form->createView(),

            ]);
    }
}
