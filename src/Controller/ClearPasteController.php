<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ClearPasteController extends AbstractController
{
    #[Route('/clear', name: 'app_clear_paste')]
    public function index(): Response
    {
        

        return $this->render('clear_paste/clear.html.twig', [

        ]);
    }
}
