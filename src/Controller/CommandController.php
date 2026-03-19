<?php

namespace App\Controller;

use App\Repository\CommandRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/command', name: 'app_command')]
final class CommandController extends AbstractController
{
    #[Route('/')]
    public function showActiveCommand(): Response
    {
        return $this->render('command/index.html.twig', [
            'controller_name' => 'CommandController',
        ]);
    }
}
