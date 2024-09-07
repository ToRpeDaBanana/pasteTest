<?php

namespace App\Controller;

use App\Entity\Paste;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\PasteCleanupService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;

class PersonalAccountController extends AbstractController
{
    #[Route('/account', name: 'app_personal_account')]
    public function index(
        PasteCleanupService $clear,
        SessionInterface $sessionInterface,
        EntityManagerInterface $entityManager,
        Request $request, 
    ): Response
    {
        $loggedIn = $sessionInterface->get('logged_in');
        $userId = $sessionInterface->get('user_id');

        // Настройка пагинации
        $limit = 10; // Ограничение количества записей на страницу
        $page = $request->query->getInt('page', 1); // Получаем номер страницы из запроса (по умолчанию 1)
        $offset = ($page - 1) * $limit;

        $pasteData = $entityManager->getRepository(Paste::class)->findBy(['user' => $userId], null, $limit, $offset);

        $total = count($entityManager->getRepository(Paste::class)->findBy(['user' => $userId]));

        $clear->cleanupExpiredPastes();
        return $this->render('personal_account/personalAccount.html.twig', [
            'pasteData' => $pasteData,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
        ]);
    }
}
