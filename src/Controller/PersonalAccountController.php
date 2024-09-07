<?php

namespace App\Controller;

use App\Entity\Paste;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\PasteCleanupService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\VarDumper\VarDumper;

class PersonalAccountController extends AbstractController
{
    #[Route('/account', name: 'app_personal_account')]
    public function index(
        PasteCleanupService $clear,
        SessionInterface $sessionInterface,
        EntityManagerInterface $entityManager,
    ): Response
    {
        $loggedIn = $sessionInterface->get('logged_in');
        $userId = $sessionInterface->get('user_id');

        $pasteData = $entityManager->getRepository(Paste::Class)->findAll(['user'=> $userId]);

        // VarDumper::dump($pasteData);
        // exit;

        $clear->cleanupExpiredPastes();
        return $this->render('personal_account/personalAccount.html.twig', [
            'pasteData'=>$pasteData,
        ]);
    }
}
