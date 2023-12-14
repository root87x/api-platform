<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Spiral\RoadRunner\Metrics\MetricsInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthenticationFailureListener
{
    public function __construct(private readonly MetricsInterface $metrics)
    {}

    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event)
    {
        $data = $event->getRequest()->toArray();

        $this->metrics->add('total_auth_failed', 1);

        $response = new JWTAuthenticationFailureResponse(
            'Пользователь с такими данными не акредитован, проверьте правильность ввода и повторите попытку.',
            JsonResponse::HTTP_UNAUTHORIZED
        );
        $response->setData($data);

        $event->setResponse($response);
    }
}