<?php

declare(strict_types=1);

namespace App\Swagger\User;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class RegisterSwaggerDecorator implements NormalizerInterface
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
                '/api/auth/register' => [
                    'post' => [
                        'tags' => ['Authentication'],
                        'operationId' => 'postCredentialsItem',
                        'summary' => 'Register a new User and get JWT token.',
                        'requestBody' => [
                            'description' => 'Create new User and return JWT token',
                            'content' => [
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
                        ],
                    ],
                ],
            ],
        ];

        return array_merge_recursive($docs, $tokenDocumentation);
    }
}
