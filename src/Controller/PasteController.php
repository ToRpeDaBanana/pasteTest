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
        // Получаем аутентифицированного пользователя
        $user = $this->getUser();
        
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

            // Получение юзера
            if ($user) {
                $paste->setUser($user); // Устанавливаем текущего аутентифицированного пользователя
            } else {
                $paste->setUser(null); // Если не аутентифицирован, присваиваем null
            }

            // Сохранение пасты в базу данных
            $entityManager->persist($paste);
            $entityManager->flush();

            // Создание полной ссылки на пасту
            if ($paste->getAccessLevel() === 'unlisted') {
                $link = $this->generateUrl('view_paste', ['uniqueId' => $uniqueId], true);
                $sessionInterface->set('linkUnlisted', $link); // Сохраняем ссылку в сессию
                $this->addFlash('success', 'Paste created successfully!');
            }

            return $this->redirectToRoute('create_paste');
        }
        $pasteData = $entityManager->getRepository(Paste::class)->findBy(
            ['accessLevel' => 'public'], // Условия поиска
            ['id' => 'DESC'], // Сортировка по дате создания (предполагается, что поле существует)
            10 // Ограничение на количество записей
        );
        $userPastes = null;
        if ($user) {
            $userPastes = $entityManager->getRepository(Paste::class)->findBy(
                ['user' => $user], // Условия поиска по пользователю
                ['id' => 'DESC'], // Сортировка по дате создания
                10 // Ограничение на количество записей
            );
        }
        $clear->cleanupExpiredPastes();

        return $this->render('paste/Paste.html.twig', [
            'form' => $form->createView(),
            'userData' => $user,
            'auth' => (bool)$user,
            'pasteData' => $pasteData,
            'total' => count($pasteData),
            'userPastes' => $userPastes, // Передаем пасты пользователя в шаблон
        ]);
    }

    #[Route('/viewPasteUnlisted/{uniqueId}', name: 'view_paste_unlisted')]
    public function viewPasteUnlisted(string $uniqueId, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $paste = $entityManager->getRepository(Paste::class)->findOneBy(['uniqueId' => $uniqueId]);

        if (!$paste || $paste->getAccessLevel() !== 'unlisted') {
            throw $this->createAccessDeniedException();
        }

        return $this->render('paste/view_paste.html.twig', [
            'paste' => $paste,
            'user' => $user,
        ]);
    }

    #[Route('/viewPaste/{id}', name: 'view_paste')]
    public function viewPaste(string $id, EntityManagerInterface $entityManager): Response
    {
        $paste = $entityManager->getRepository(Paste::class)->find($id);

        if (!$paste) {
            throw $this->createNotFoundException('Паста не найдена.');
        }

        $user = $this->getUser();

        // Проверяем уровень доступа
        if ($paste->getAccessLevel() === 'private' && $paste->getUser() !== $user) {
            throw $this->createAccessDeniedException(); // Доступ запрещен
        }

        return $this->render('paste/view_paste.html.twig', [
            'paste' => $paste,
            'user' => $user,
        ]);
    }

}
