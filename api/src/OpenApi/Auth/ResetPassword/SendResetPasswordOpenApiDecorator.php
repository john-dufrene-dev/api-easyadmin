<?php

declare(strict_types=1);

namespace App\OpenApi\Auth\ResetPassword;

use ApiPlatform\Core\OpenApi\Model;
use ApiPlatform\Core\OpenApi\OpenApi;
use Symfony\Component\HttpFoundation\Response;
use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;

final class SendResetPasswordOpenApiDecorator implements OpenApiFactoryInterface
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
            'Send Reset Password', // Ref
            'Send request to receive secret.',  // Summary
            'Send request to receive secret',     // Description
            null,                       // Operation GET
            null,                       // Operation PUT
            new Model\Operation(        // Operation POST
                'postsendResetPasswordItem',  // OperationId
                ['User Reset Password'],                    // Tags
                [                      // Responses
                    Response::HTTP_OK => [
                        'description' => 'Get Secret token and receive secret by email',
                        'content' => [
                            'application/ld+json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Auth.GetSecretToken',
                                ],
                            ],
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Auth.GetSecretToken',
                                ],
                            ],
                        ],
                    ],
                    Response::HTTP_METHOD_NOT_ALLOWED => [
                        'description' => 'Method Not Allowed (Allow: {POST})',
                    ],
                    Response::HTTP_UNSUPPORTED_MEDIA_TYPE => [
                        'description' => 'Invalid content type Header (Allow: {application/json & application/ld+json})',
                    ],
                    Response::HTTP_BAD_REQUEST => [
                        'description' => 'Bad Request',
                    ],
                ],
                'Send request to receive secret.', // Summary
                '',                        // Description
                null,                      // External Docs
                [],                        // Parameters
                new Model\RequestBody(     // RequestBody
                    'Send request to receive secret',           // Description
                    new \ArrayObject([                   // Content
                        'application/ld+json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Auth.SendResetPassword',
                            ],
                        ],
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Auth.SendResetPassword',
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
        $openApi->getPaths()->addPath('/api/auth/reset/send', $pathItem);

        return $openApi;
    }
}
