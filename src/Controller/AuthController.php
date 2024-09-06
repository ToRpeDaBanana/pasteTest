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


class AuthController extends AbstractController
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

        return $this->render('auth/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/login', name: 'app_login', methods:['GET','POST'])]
    public function login(
        Request $request,
        EntityManagerInterface $entityManager,
        AuthenticationUtils $authenticationUtils,
        SessionInterface $sessionInterface,
    ): Response
        {

            $form = $this->createForm(LoginForm::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $username = $data['username'];
                $password = $data['password'];
                $user = $entityManager->getRepository(User::Class)->findOneBy(['username'=> $username,]);
                // VarDumper::dump(password_verify($password, $user->getPassword()));
                if($user && password_verify($password, $user->getPassword()))
                {
                    $sessionInterface->set('logged_in', true);
                    $sessionInterface->set('user_id', $user->getId());
                    return $this->redirectToRoute('create_paste');
                }
                else{
                    $this->addFlash('error', 'Неверные учетные данные.');
                }

            }

            return $this->render('auth/login.html.twig', [
                'form' => $form->createView(),
                ]);
        }
        
        #[Route(path: '/logout', name: 'app_logout')]
        public function logout(): void
        {
            throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
        }
}
