<?php

declare(strict_types=1);

namespace App\Swagger\User;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class AuthenticationTokenSwaggerDecorator implements NormalizerInterface
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
                '/api/auth/login_check' => [
                    'post' => [
                        'tags' => ['Authentication'],
                        'operationId' => 'postAuthenticationToken',
                        'summary' => 'Get JWT token to login.',
                        'requestBody' => [
                            'description' => 'Create new JWT Token',
                            'content' => [
                                'application/ld+json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/Credentials',
                                    ],
                                ],
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/Credentials',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'Get JWT token',
                                'content' => [
                                    'application/ld+json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/Token',
                                        ],
                                    ],
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/Token',
                                        ],
                                    ],
                                ],
                            ],
                            Response::HTTP_BAD_REQUEST => [
                                'description' => 'Bad Request',
                            ],
                            Response::HTTP_UNAUTHORIZED => [
                                'description' => 'Invalid Credentials',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return array_merge_recursive($docs, $tokenDocumentation);
    }
}
