<?php

declare(strict_types=1);

namespace App\Swagger;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ComponentsSwaggerDecorator implements NormalizerInterface
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

        // Component Token
        $docs['components']['schemas']['Token'] = [
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
        ];

        // Component Refresh Token
        $docs['components']['schemas']['RefreshToken'] = [
            'type' => 'object',
            'properties' => [
                'refresh_token' => [
                    'type' => 'string',
                    'example' => 'your_refresh_token',
                ],
            ],
        ];

        // Component Credentials
        $docs['components']['schemas']['Credentials'] = [
            'type' => 'object',
            'properties' => [
                'email' => [
                    'type' => 'string',
                    'example' => 'email@email.com',
                ],
                'password' => [
                    'type' => 'string',
                    'example' => 'your_password',
                ],
            ],
        ];

        return $docs;
    }
}
