<?php

declare(strict_types=1);

namespace App\OpenApi\Auth;

use ApiPlatform\Core\OpenApi\Model;
use ApiPlatform\Core\OpenApi\OpenApi;
use Symfony\Component\HttpFoundation\Response;
use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;

final class AuthenticationTokenOpenApiDecorator implements OpenApiFactoryInterface
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

        $pathItem = new Model\PathItem(
            'JWT Token',                // Ref
            'Get JWT token to login.',  // Summary
            'Create new JWT Token',     // Description
            null,                       // Operation GET
            null,                       // Operation PUT
            new Model\Operation(        // Operation POST
                'postAuthenticationToken',  // OperationId
                ['User Authentication'],                    // Tags
                [                      // Responses
                    Response::HTTP_OK => [
                        'description' => 'Get JWT token',
                        'content' => [
                            'application/ld+json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Auth.Token',
                                ],
                            ],
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Auth.Token',
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
                    Response::HTTP_METHOD_NOT_ALLOWED => [
                        'description' => 'Method Not Allowed (Allow: {POST})',
                    ],
                ],
                'Get JWT token to login.', // Summary
                '',                        // Description
                null,                      // External Docs
                [],                        // Parameters
                new Model\RequestBody(     // RequestBody
                    'Create new JWT Token',           // Description
                    new \ArrayObject([                   // Content
                        'application/ld+json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Auth.Credentials',
                            ],
                        ],
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Auth.Credentials',
                            ],
                        ],
                    ]),
                    false                               // Required
                ),
                null,                      // Callbacks
                false,                     // Deprecated
                [],                      // Security
                null,                      // Servers
            ),
            null,                // Operation DELETE
            null,                // Operation OPTIONS
            null,                // Operation HEAD
            null,                // Operation PATCH
            null,                // Operation TRACE
            null,                // Servers
            [],                  // Parameters
        );
        $openApi->getPaths()->addPath('/api/auth/login_check', $pathItem);

        return $openApi;
    }
}
