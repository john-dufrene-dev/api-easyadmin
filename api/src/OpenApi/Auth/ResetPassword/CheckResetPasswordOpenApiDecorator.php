<?php

declare(strict_types=1);

namespace App\OpenApi\Auth\ResetPassword;

use ApiPlatform\Core\OpenApi\Model;
use ApiPlatform\Core\OpenApi\OpenApi;
use Symfony\Component\HttpFoundation\Response;
use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;

final class CheckResetPasswordOpenApiDecorator implements OpenApiFactoryInterface
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
            'Check Token and Secret', // Ref
            'Check if Token and Secret are valid.',  // Summary
            'Check if Token and Secret are valid',     // Description
            null,                       // Operation GET
            null,                       // Operation PUT
            new Model\Operation(        // Operation POST
                'postcheckResetPasswordItem',  // OperationId
                ['User Reset Password'],                    // Tags
                [                      // Responses
                    Response::HTTP_OK => [
                        'description' => 'Check if Token and Secret are valid',
                        'content' => [
                            'application/ld+json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Auth.GetTokenResetPassword',
                                ],
                            ],
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Auth.GetTokenResetPassword',
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
                'Check if Token and Secret are valid.', // Summary
                '',                        // Description
                null,                      // External Docs
                [],                        // Parameters
                new Model\RequestBody(     // RequestBody
                    'Check if Token and Secret are valid',           // Description
                    new \ArrayObject([                   // Content
                        'application/ld+json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Auth.CheckResetPassword',
                            ],
                        ],
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Auth.CheckResetPassword',
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
        $openApi->getPaths()->addPath('/api/auth/reset/check', $pathItem);

        return $openApi;
    }
}
