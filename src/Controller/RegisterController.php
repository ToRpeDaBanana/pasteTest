<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterForm;
use App\Form\LoginForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\VarDumper\VarDumper;


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
            // Хешируем пароль перед сохранением
            $user->setPassword(password_hash($user->getPassword(), PASSWORD_BCRYPT));
            
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'User registered successfully!');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('register/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}