<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
class ErrorController extends AbstractController
{
    #[Route('/error/404', name: 'error_404')]
    public function show404(): Response
    {
        return $this->render('404.html.twig', [
        ]);
    }
}
