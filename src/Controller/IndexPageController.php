<?php

namespace App\Controller;

use App\Entity\Paste;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\PasteCleanupService;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class IndexPageController extends AbstractController
{
    #[Route('/', name: 'app_index_page')]
    public function index(
        PasteCleanupService $clear,
        EntityManagerInterface $entityManager,
        Request $request, 
    ): Response
    {   
        $user = $this->getUser(); // Получаем текущего авторизованного пользователя

        // Получаем публичные пасты
        $pasteData = $entityManager->getRepository(Paste::class)->findBy(
            ['accessLevel' => 'public'], // Условия поиска
            ['id' => 'DESC'], // Сортировка последних записей
            10 // Ограничение на количество записей
        );

        // Получаем последние 10 паст текущего пользователя, если он авторизован
        $userPastes = null;
        if ($user) {
            $userPastes = $entityManager->getRepository(Paste::class)->findBy(
                ['user' => $user], // Условия поиска по пользователю
                ['id' => 'DESC'], // Сортировка последних записей
                10 // Ограничение на количество записей
            );
        }

        $clear->cleanupExpiredPastes();

        return $this->render('index_page/index.html.twig', [
            'pasteData' => $pasteData,
            'total' => count($pasteData),
            'userPastes' => $userPastes, // Передаем пасты пользователя в шаблон
        ]);
    }
}

