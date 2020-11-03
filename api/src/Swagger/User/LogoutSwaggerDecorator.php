<?php

declare(strict_types=1);

namespace App\Swagger\User;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class LogoutSwaggerDecorator implements NormalizerInterface
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
                '/api/auth/logout' => [
                    'post' => [
                        'tags' => ['Authentication'],
                        'operationId' => 'postLogoutItem',
                        'summary' => 'Logout the User',
                        'requestBody' => [
                            'description' => 'Logout the current User',
                            'content' => [
                                'application/ld+json' => [],
                                'application/json' => [],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'User succesfully logout',
                            ],
                            Response::HTTP_METHOD_NOT_ALLOWED => [
                                'description' => 'Method Not Allowed (Allow: {POST})',
                            ],
                            Response::HTTP_UNAUTHORIZED => [
                                'description' => 'Unauthorized',
                            ],
                            Response::HTTP_UNSUPPORTED_MEDIA_TYPE => [
                                'description' => 'Invalid content type Header (Allow: {application/json & application/ld+json})',
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
