<?php

namespace App\EventSubscriber;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Throwable;

class ExceptionSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => [
                ['onKernelException', 9]
            ]
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }
        $exception = $event->getThrowable();

        if ($exception instanceof BadRequestHttpException) {
            $response = new JsonResponse($this->buildResponseData($exception));
            $response->setStatusCode(400);

            $event->setResponse($response);
        }
    }

    #[ArrayShape(['title' => "string", 'detail' => "string", 'type' => "string"])]
    private function buildResponseData(
        Throwable $exception
    ): array {
        return [
            'title' => 'An error occurred',
            'detail' => $exception->getMessage(),
            'type' => 'https://tools.ietf.org/html/rfc2616#section-10'
        ];
    }
}
