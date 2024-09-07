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
        $pasteData = $entityManager->getRepository(Paste::class)->findBy(
            ['accessLevel' => 'public'], // Условия поиска
            ['expirationTime' => 'DESC'], // Сортировка по дате создания (предполагается, что поле существует)
            10 // Ограничение на количество записей
        );

        $clear->cleanupExpiredPastes();

        return $this->render('index_page/index.html.twig', [
            'pasteData' => $pasteData,
            'total' => count($pasteData),
        ]);
    }
}
