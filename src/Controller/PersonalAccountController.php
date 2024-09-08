<?php

namespace App\Controller;

use App\Entity\Paste; // Импортируем сущность Paste
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\PasteCleanupService; // Сервис для очистки устаревших паст
use Doctrine\ORM\EntityManagerInterface; // Интерфейс для работы с менеджером сущностей
use Symfony\Component\HttpFoundation\Session\SessionInterface; // Интерфейс для работы с сессией
use Symfony\Component\HttpFoundation\Request;

class PersonalAccountController extends AbstractController
{
    #[Route('/account', name: 'app_personal_account')]
    public function index(
        PasteCleanupService $clear, // Сервис для очистки устаревших паст
        SessionInterface $sessionInterface, // Сессия пользователя
        EntityManagerInterface $entityManager, // Менеджер сущностей для работы с БД
        Request $request, // Объект запроса
    ): Response {
        // Получаем аутентифицированного пользователя
        $user = $this->getUser();
        
        // Проверка, что пользователь аутентифицирован
        if (!$user) {
            return $this->redirectToRoute('app_login'); // Перенаправляем на страницу логина, если пользователь не аутентифицирован
        }

        $userId = $user->getId(); // Получаем ID пользователя
        
        // Настройка пагинации
        $limit = 10; // Ограничение количества записей на странице
        $page = $request->query->getInt('page', 1); // Получаем номер страницы из запроса (по умолчанию 1)
        $offset = ($page - 1) * $limit; // Считаем смещение для выборки записей

        // Получаем пасты, связанные с пользователем, с учетом пагинации
        $pasteData = $entityManager->getRepository(Paste::class)->findBy(['user' => $userId], null, $limit, $offset);
        // Получаем общее количество паст пользователя
        $total = count($entityManager->getRepository(Paste::class)->findBy(['user' => $userId]));

        // Очистка устаревших паст с помощью сервиса
        $clear->cleanupExpiredPastes();
        
        // Отправка данных в шаблон для отображения
        return $this->render('personal_account/personalAccount.html.twig', [
            'pasteData' => $pasteData, // Передаем пасты пользователя в шаблон
            'total' => $total, // Общее количество паст пользователя
            'page' => $page, // Текущая страница
            'limit' => $limit, // Ограничение на количество записей
        ]);
    }
}
