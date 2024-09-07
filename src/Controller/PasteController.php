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
            $paste->setAccessLevel($form->get('accessLevel')->getData());

            // Генерация уникального URL для пасты
            $uniqueId = md5(uniqid((string) rand(), true));
            $paste->setUniqueId($uniqueId);

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

            if ($paste->getAccessLevel() === 'unlisted') {
                // Создание полной ссылки на пасту
                $link = $this->generateUrl('view_paste', ['uniqueId' => $uniqueId], true);
                // Сохранение ссылки на пасту в сессии для отображения в поп-апе
                $sessionInterface->set('linkUnlisted', $link);
                $this->addFlash('success', 'Paste created successfully!');
            } else {
                $this->addFlash('success', 'Paste created successfully!');
            }

            $this->addFlash('success', 'Paste created successfully!');
            return $this->redirectToRoute('create_paste');
        }
            
        $clear->cleanupExpiredPastes();
        

        return $this->render('paste/Paste.html.twig', [
            'form' => $form->createView(),
            'userData'=>$user=$entityManager->getRepository(User::Class)->findOneBy(['id'=> $userId]),
            'auth'=>$loggedIn,
            ]);
    }
    
    #[Route('/viewPaste/{uniqueId}', name: 'view_paste')]
    public function viewPaste(string $uniqueId, EntityManagerInterface $entityManager): Response
    {
        $paste = $entityManager->getRepository(Paste::class)->findOneBy(['uniqueId' => $uniqueId]);

        if (!$paste || $paste->getAccessLevel() !== 'unlisted') {
            throw $this->createAccessDeniedException();
        }

        return $this->render('paste/view_paste.html.twig', [
            'paste' => $paste,
        ]);
    }
}
