<?php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;

final class ComponentsOpenApiDecorator implements OpenApiFactoryInterface
{
    /**
     * decorated
     *
     * @var mixed
     */
    private $decorated;

    /**
     * __construct
     *
     * @param  mixed $decorated
     * @return void
     */
    public function __construct(OpenApiFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * __invoke
     *
     * @param  mixed $context
     * @return OpenApi
     */
    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        // Component Response Token
        $schemas['Auth.Token'] = new \ArrayObject([
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

        // Component Request Refresh Token
        $schemas['Auth.RefreshToken'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'refresh_token' => [
                    'type' => 'string',
                    'example' => 'your_refresh_token',
                ],
            ],
        ]);

        // Component Request Credentials
        $schemas['Auth.Credentials'] = new \ArrayObject([
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
        ]);

        // Component Request Send Secret
        $schemas['Auth.SendResetPassword'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'email' => [
                    'type' => 'string',
                    'example' => 'email@email.com',
                ],
            ],
        ]);

        // Component Response Send Secret
        $schemas['Auth.GetSecretToken'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'code' => [
                    'type' => 'integer',
                    'example' => 200,
                    'readOnly' => true,
                ],
                'message' => [
                    'type' => 'string',
                    'example' => 'Secret Successfully send secret password',
                    'readOnly' => true,
                ],
                'token' => [
                    'type' => 'string',
                    'example' => '01F3R817DP5CNRGRC9H356CAQW',
                    'readOnly' => true,
                ],
            ],
        ]);

        return $openApi;
    }
}
