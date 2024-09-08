<?php

namespace App\Controller;

use App\Entity\User; // Импортируем сущность пользователя
use App\Form\RegisterForm; // Импортируем форму регистрации
use Doctrine\ORM\EntityManagerInterface; // Интерфейс для работы с менеджером сущностей
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RegisterController extends AbstractController
{
    // Определяем маршрут для страницы регистрации
    #[Route('/register', name: 'register', methods:['GET','POST'])]
    public function register(
        Request $request, // Объект запроса
        EntityManagerInterface $entityManager // Менеджер сущностей для работы с БД
    ): Response
    {
        $user = new User(); // Создаем новый объект пользователя
        $form = $this->createForm(RegisterForm::class, $user); // Создаем форму регистрации для нового пользователя

        $form->handleRequest($request); // Обрабатываем входящий запрос
        
        if ($form->isSubmitted() && $form->isValid()) { // Проверяем, была ли форма отправлена и валидна ли она
            // Проверяем, существует ли уже пользователь с таким именем
            $existingUser = $entityManager->getRepository(User::class)->findOneBy([
                'username' => $user->getUsername(), // Поиск по имени пользователя
            ]);

            if ($existingUser) {
                // Добавляем сообщение об ошибке, если пользователь с таким именем уже существует
                $this->addFlash('error', 'Пользователь с таким именем уже существует.');
            } else {
                // Хешируем пароль перед сохранением в БД
                $user->setPassword(password_hash($user->getPassword(), PASSWORD_BCRYPT));
                
                $entityManager->persist($user); // Указываем, что новый пользователь должен быть сохранен
                $entityManager->flush(); // Сохраняем изменения в БД

                // Добавляем сообщение об успешной регистрации
                $this->addFlash('success', 'Пользователь зарегистрирован успешно!');

                // Перенаправляем пользователя на страницу входа
                return $this->redirectToRoute('app_login');
            }
        }

        // Отображаем форму регистрации, если она не была отправлена или невалидна
        return $this->render('register/register.html.twig', [
            'form' => $form->createView(), // Передаем представление формы в шаблон
        ]);
    }
}
