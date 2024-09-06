<?php

namespace App\Service;

use App\Repository\PasteRepository;
use Doctrine\ORM\EntityManagerInterface;

class PasteCleanupService
{
    private EntityManagerInterface $entityManager;
    private PasteRepository $pasteRepository;

    public function __construct(EntityManagerInterface $entityManager, PasteRepository $pasteRepository)
    {
        $this->entityManager = $entityManager;
        $this->pasteRepository = $pasteRepository;
    }

    public function cleanupExpiredPastes(): void
    {
        // Получаем все устаревшие пасты
        $expiredPastes = $this->pasteRepository->findExpiredPastes();

        foreach ($expiredPastes as $paste) {
            $this->entityManager->remove($paste);
        }

        $this->entityManager->flush();
    }
}