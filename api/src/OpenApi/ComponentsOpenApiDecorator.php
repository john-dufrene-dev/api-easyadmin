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
                    'example' => 'tf5fggrg4g59g5g.b48b4tg8t4y84ds4gt84e8f4r8gr4tht4yh4d8g4ra8d55.F5z4hns54h',
                    'readOnly' => true,
                ],
                'refresh_token' => [
                    'type' => 'string',
                    'example' => 'f5fggrg4g59g5g.b48b4tg8t4y84ds4gt84e8f4r8gr4tht4yh4d8g4ra8d55.F5z4hns54h',
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
                    'example' => 'f5fggrg4g59g5g.b48b4tg8t4y84ds4gt84e8f4r8gr4tht4yh4d8g4ra8d55.F5z4hns54h',
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
                    'example' => 'D8g8_#[dj58SSX_85',
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
