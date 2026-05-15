<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Twig\Environment;

class ExceptionSubscriber implements EventSubscriberInterface
{

    public function __construct(private Environment $twig)
    {}
    
    public function onExceptionEvent(ExceptionEvent $event): void
    {
        
        $exception = $event->getThrowable();
        $request = $event->getRequest();
        
        // gestion du code erreur
        $statusCode = 500; 
        if ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();
        }

        $message = $this->getMessage($statusCode);
        
        // Vérifie si la route commence par /api, pour chosir le format de retour
        if (strpos($request->getPathInfo(), '/api') === 0) {

            $response = new JsonResponse([
                'error' => [
                    'code' => $statusCode,
                    'message' => $message,
                ],
            ], $statusCode);

            $event->setResponse($response);
        } else {
            $render = $this->twig->render('error.html.twig', [
                'code' => $statusCode,
                'message' => $message,
            ]);
            $event->setResponse(new Response($render, $statusCode));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => 'onExceptionEvent',
        ];
    }

    private function getMessage(int $code): string
    {
        $texts = [
            404 => 'Not Found',
            500 => 'Internal Server Error',
            403 => 'Forbidden',
            400 => 'Bad Request',
        ];
        return $texts[$code] ?? 'Error';
    }
}
