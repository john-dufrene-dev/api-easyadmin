<?php

declare(strict_types=1);

namespace App\Swagger\User;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class RefreshTokenSwaggerDecorator implements NormalizerInterface
{
    private $decorated;

    public function __construct(NormalizerInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $this->decorated->supportsNormalization($data, $format);
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        $docs = $this->decorated->normalize($object, $format, $context);

        $tokenDocumentation = [
            'paths' => [
                '/api/auth/refresh_token' => [
                    'post' => [
                        'tags' => ['Authentication'],
                        'operationId' => 'postRefreshToken',
                        'summary' => 'Refresh Token to login.',
                        'requestBody' => [
                            'description' => 'Create new JWT Token',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/RefreshToken',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'Get JWT token',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/RefreshToken',
                                        ],
                                    ],
                                ],
                            ],
                            Response::HTTP_UNAUTHORIZED => [
                                'description' => 'An authentication exception occurred',
                            ],
                            Response::HTTP_BAD_REQUEST => [
                                'description' => 'Bad Request',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return array_merge_recursive($docs, $tokenDocumentation);
    }
}
