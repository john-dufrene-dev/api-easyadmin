<?php

namespace App\Service\Api\Builder;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * ApiResponseBuilder
 */
class ApiResponseBuilder
{
    public const CONTENT_TYPE = ['application/json', 'application/ld+json'];

    // @Todo : translations all messages

    /**
     * CheckIfMethodPost
     *
     * @return void
     */
    public function CheckIfMethodPost()
    {
        return new JsonResponse([
            "code" => Response::HTTP_METHOD_NOT_ALLOWED,
            "message" => 'Method Not Allowed (Allow: {POST})'
        ], Response::HTTP_METHOD_NOT_ALLOWED);
    }

    /**
     * checkIfAcceptContentType
     *
     * @return void
     */
    public function checkIfAcceptContentType()
    {
        return new JsonResponse([
            "code" => Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
            "message" => 'Invalid content type Header (Allow: {application/json & application/ld+json})'
        ], Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
    }

    /**
     * checkIfBodyIsEmpty
     *
     * @return void
     */
    public function checkIfBodyIsEmpty()
    {
        return new JsonResponse([
            "code" => Response::HTTP_BAD_REQUEST,
            "message" => 'Bad Request : Body content is empty'
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * checkifUnauthorized
     *
     * @return void
     */
    public function checkIfUnauthorized()
    {
        return new JsonResponse([
            "code" => Response::HTTP_UNAUTHORIZED,
            "message" => 'Unauthorized'
        ], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * checkIfBadRequest
     *
     * @return void
     */
    public function checkIfBadRequest()
    {
        return new JsonResponse([
            "code" => Response::HTTP_BAD_REQUEST,
            "message" => 'Bad Request'
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * checkIfErrorsBadRequest
     *
     * @param  mixed $errs
     * @return void
     */
    public function checkIfErrorsBadRequest($errs)
    {
        return new JsonResponse([
            "code" => Response::HTTP_BAD_REQUEST,
            "message" => array_values(array_unique($errs))
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * checkIfBadQueryParameters
     *
     * @return void
     */
    public function checkIfBadQueryParameters()
    {
        return new JsonResponse([
            "code" => Response::HTTP_BAD_REQUEST,
            "message" => 'Bad Request : Bad query parameter'
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * checkIfInvalidQueryParameters
     *
     * @return void
     */
    public function checkIfInvalidQueryParameters()
    {
        return new JsonResponse([
            "code" => Response::HTTP_BAD_REQUEST,
            "message" => 'Bad Request : Invalid Query parameter'
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * checkIfInvalidUser
     *
     * @return void
     */
    public function checkIfInvalidUser()
    {
        return new JsonResponse([
            "code" => Response::HTTP_BAD_REQUEST,
            "message" => 'Bad Request : Invalid user'
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * checkIfTooManyTry
     *
     * @return void
     */
    public function checkIfTooManyTry()
    {
        return new JsonResponse([
            "code" => Response::HTTP_BAD_REQUEST,
            "message" => 'Bad Request : Too many try',
        ], Response::HTTP_BAD_REQUEST);
    }
}
