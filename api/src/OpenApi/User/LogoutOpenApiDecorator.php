<?php

declare(strict_types=1);

namespace App\OpenApi\User;

use ApiPlatform\Core\OpenApi\Model;
use ApiPlatform\Core\OpenApi\OpenApi;
use Symfony\Component\HttpFoundation\Response;
use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;

final class LogoutOpenApiDecorator implements OpenApiFactoryInterface
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
            'Logout',                // Ref
            'Logout the User.',  // Summary
            'Logout the current User',     // Description
            null,                       // Operation GET
            null,                       // Operation PUT
            new Model\Operation(        // Operation POST
                'postLogoutItem',  // OperationId
                ['User Authentication'],                    // Tags
                [                      // Responses
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
                'Logout the current User.', // Summary
                '',                        // Description
                null,                      // External Docs
                [],                        // Parameters
                new Model\RequestBody(     // RequestBody
                    'Logout the current User',           // Description
                    new \ArrayObject([                   // Content
                        'application/ld+json' => [],
                        'application/json' => [],
                    ]),
                    false                               // Required
                ),
                null,                      // Callbacks
                false,                     // Deprecated
                null,                      // Security
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
        $openApi->getPaths()->addPath('/api/auth/logout', $pathItem);

        return $openApi;
    }
}
