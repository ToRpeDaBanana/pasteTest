<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'register', methods:['GET','POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterForm::class, $user);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Проверяем, существует ли уже пользователь с таким именем
            $existingUser = $entityManager->getRepository(User::class)->findOneBy([
                'username' => $user->getUsername(),
            ]);

            if ($existingUser) {
                $this->addFlash('error', 'Пользователь с таким именем уже существует.');
            } else {
                // Хешируем пароль перед сохранением
                $user->setPassword(password_hash($user->getPassword(), PASSWORD_BCRYPT));
                
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Пользователь зарегистрирован успешно!');

                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('register/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
