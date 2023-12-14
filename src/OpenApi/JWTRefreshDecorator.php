<?php
// api/src/OpenApi/JwtDecorator.php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\OpenApi;
use ApiPlatform\OpenApi\Model;

final class JWTRefreshDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $decorated
    ) {}

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['RefreshToken'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                'refresh_token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ]);
        $schemas['RefreshTokenCredentials'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'refresh_token' => [
                    'type' => 'string',
                    'example' => '9c31a97c729474689bf3534c0bba417c5f76f71e4b358c22dbe4b2b7ee35037f8a0e2523e47a93c7c3ee0f6ad0bba64dba9529311e5f74671b67895f80e0dba7',
                ],
            ],
        ]);
        
        $pathItem = new Model\PathItem(
            ref: 'JWT Refresh Token',
            post: new Model\Operation(
                operationId: 'postCredentialsItem',
                tags: ['RefreshToken'],
                responses: [
                    '200' => [
                        'description' => 'Токены авторизации и обновления.',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/RefreshToken',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Обновить токен авторизации.',
                requestBody: new Model\RequestBody(
                    description: 'Генерировать новый токен авторизации',
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/RefreshTokenCredentials',
                            ],
                        ],
                    ]),
                ),
                security: [],
            ),
        );
        $openApi->getPaths()->addPath('/api/authentication_refresh_token', $pathItem);

        return $openApi;
    }
}