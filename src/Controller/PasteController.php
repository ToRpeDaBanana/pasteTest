<?php

namespace App\Controller;
use App\Entity\Paste;
use App\Entity\User;
use App\Service\PasteCleanupService;
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
    
    #[Route('/createPaste', name: 'create_paste', methods:['GET','POST'])]
    public function createPaste(
        Request $request, 
        EntityManagerInterface $entityManager,
        SessionInterface $sessionInterface,
        PasteCleanupService $clear,
    ): Response
    {
        $loggedIn = $sessionInterface->get('logged_in');
        $userId = $sessionInterface->get('user_id');
        $paste = new Paste();
        $form = $this->createForm(PasteForm::class, $paste);

        $form->handleRequest($request);

        

        if ($form->isSubmitted() && $form->isValid()) {
            $expirationTime = $form->get('expirationTime')->getData();
            $paste->setExpirationTime($expirationTime);
            

            // получение юзера
            if($loggedIn != false)
            {
                $user = $entityManager->getRepository(User::Class)->findOneBy(['id'=> $userId]);
                $paste->setUser($user);
            }
            else{
                $paste->setUser(null);
            }
            
            // Сохранение пасты в базу данных
            $entityManager->persist($paste);
            $entityManager->flush();

            $this->addFlash('success', 'Paste created successfully!');
        }
            
        $clear->cleanupExpiredPastes();
        // $clear = $clearPaste;
        // if(flash == 'success')
        // {
        //     return $this->redirectToRoute('app_index_page');
        // }
        

        return $this->render('paste/Paste.html.twig', [
            'form' => $form->createView(),

            ]);
    }
}
