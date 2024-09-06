<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AuthController extends AbstractController
{
    #[Route('/register', name: 'register', methods:['GET','POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $em
    ): Response
    {
        $data = json_decode($request->getContent(), true);

    // Проверяем, является ли декодированные данные массивом
    if (!is_array($data)) {
        return new Response('Invalid data format', Response::HTTP_BAD_REQUEST);
    }

    // Проверяем наличие необходимых полей
    if (!isset($data['username']) || !isset($data['password'])) {
        return new Response('Username and password are required', Response::HTTP_BAD_REQUEST);
    }

    $user = new User();
    $user->setUsername($data['username']);
    $user->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
    
    $em->persist($user);
    $em->flush();

    return new Response('User registered', Response::HTTP_CREATED);
    }
}
