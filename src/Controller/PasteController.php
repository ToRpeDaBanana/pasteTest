<?php

namespace App\Controller;

use App\Entity\Paste; // Импортируем сущность Paste
use App\Entity\User; // Импортируем сущность User
use App\Service\PasteCleanupService; // Импортируем сервис для очистки устаревших паст
use App\Form\PasteForm; // Импортируем форму для пасты
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface; // Интерфейс для работы с базой данных
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface; // Интерфейс сессии

class PasteController extends AbstractController
{
    #[Route('/createPaste', name: 'create_paste', methods: ['GET', 'POST'])]
    public function createPaste(
        Request $request, // HTTP-запрос
        EntityManagerInterface $entityManager, // Менеджер сущностей
        SessionInterface $sessionInterface, // Сессия пользователя
        PasteCleanupService $clear, // Сервис для очистки устаревших паст
    ): Response {
        // Получаем текущего аутентифицированного пользователя
        $user = $this->getUser();

        // Создаем новый объект Paste
        $paste = new Paste();
        // Создаем форму для пасты на основе PasteForm
        $form = $this->createForm(PasteForm::class, $paste);

        // Обработка запроса формы
        $form->handleRequest($request);

        // Проверяем, была ли форма отправлена и действительна ли она
        if ($form->isSubmitted() && $form->isValid()) {
            // Устанавливаем время истечения пасты
            $expirationTime = $form->get('expirationTime')->getData();
            $paste->setExpirationTime($expirationTime);
            // Устанавливаем уровень доступа
            $paste->setAccessLevel($form->get('accessLevel')->getData());

            // Генерация уникального идентификатора для пасты
            $uniqueId = md5(uniqid((string) rand(), true));
            $paste->setUniqueId($uniqueId);

            // Устанавливаем аутентифицированного пользователя к пасте
            if ($user) {
                $paste->setUser($user);
            } else {
                $paste->setUser(null); // Если пользователь не аутентифицирован
            }

            // Сохраняем пасту в базу данных
            $entityManager->persist($paste);
            $entityManager->flush();

            // Генерация ссылки на пасту, если она не публичная
            if ($paste->getAccessLevel() === 'unlisted') {
                $link = $this->generateUrl('view_paste_unlisted', ['uniqueId' => $uniqueId], true);
                $sessionInterface->set('linkUnlisted', $link); // Сохраняем ссылку в сессии
                $this->addFlash('success', 'Paste created successfully!'); // Уведомление об успешном создании
            }

            // Перенаправляем на страницу создания пасты после успешного выполнения
            return $this->redirectToRoute('create_paste');
        }

        // Получаем последние 10 публичных паст
        $pasteData = $entityManager->getRepository(Paste::class)->findBy(
            ['accessLevel' => 'public'],
            ['id' => 'DESC'],
            10
        );

        $userPastes = null; // Инициализируем переменную для паст пользователя
        if ($user) {
            // Если пользователь аутентифицирован, получаем его пасты
            $userPastes = $entityManager->getRepository(Paste::class)->findBy(
                ['user' => $user],
                ['id' => 'DESC'],
                10
            );
        }

        // Очистка устаревших паст с помощью сервиса
        $clear->cleanupExpiredPastes();

        // Отправка данных в шаблон для отображения
        return $this->render('paste/Paste.html.twig', [
            'form' => $form->createView(),
            'userData' => $user,
            'auth' => (bool)$user, // проверка, аутентифицирован ли пользователь
            'pasteData' => $pasteData, // последняя паста
            'total' => count($pasteData), // общее количество паст
            'userPastes' => $userPastes, // пасты аутентифицированного пользователя
        ]);
    }

    #[Route('/viewPasteUnlisted/{uniqueId}', name: 'view_paste_unlisted')]
    public function viewPasteUnlisted(string $uniqueId, EntityManagerInterface $entityManager): Response {
        $user = $this->getUser(); // Получаем текущего пользователя
        // Поиск пасты по уникальному идентификатору
        $paste = $entityManager->getRepository(Paste::class)->findOneBy(['uniqueId' => $uniqueId]);

        // Проверяем, существует ли паста и является ли она невыставленной
        if (!$paste || $paste->getAccessLevel() !== 'unlisted') {
            throw $this->createAccessDeniedException(); // Отказ в доступе
        }

        // Отправка данных пасты в шаблон
        return $this->render('paste/view_paste.html.twig', [
            'paste' => $paste,
            'user' => $user,
        ]);
    }

    #[Route('/viewPaste/{id}', name: 'view_paste')]
    public function viewPaste(string $id, EntityManagerInterface $entityManager): Response {
        // Поиск пасты по ID
        $paste = $entityManager->getRepository(Paste::class)->find($id);

        // Проверка, существует ли паста
        if (!$paste) {
            throw $this->createNotFoundException('Паста не найдена.'); // Не найдено
        }

        $user = $this->getUser(); // Получаем текущего пользователя

        // Проверка уровня доступа к пасте
        if ($paste->getAccessLevel() === 'private' && $paste->getUser() !== $user) {
            throw $this->createAccessDeniedException(); // Доступ запрещен
        }

        // Отправка данных пасты в шаблон
        return $this->render('paste/view_paste.html.twig', [
            'paste' => $paste,
            'user' => $user,
        ]);
    }

    #[Route('/clear-link-unlisted', name: 'clear_link_unlisted', methods: ['POST'])]
    public function clearLinkUnlisted(SessionInterface $sessionInterface): Response {
        // Удаляем ссылку на невыставленную пасту из сессии
        $sessionInterface->remove('linkUnlisted');
        return new Response(null, Response::HTTP_NO_CONTENT); // Возвращаем пустой ответ
    }
}
