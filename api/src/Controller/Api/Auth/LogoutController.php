<?php

namespace App\Controller\Api\Auth;

use App\Entity\Customer\UserToken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api")
 */
class LogoutController extends AbstractController
{
    public const CONTENT_TYPE = ['application/json', 'application/ld+json'];

    /**
     * em
     *
     * @var mixed
     */
    private $em;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/auth/logout", name="api_logout")
     * 
     * @param  mixed $request
     * @return JsonResponse
     */
    public function logoutApi(Request $request): JsonResponse
    {
        // Tcheck if POST Method
        if (!$request->isMethod('POST')) {

            return $this->json([
                "code" => Response::HTTP_METHOD_NOT_ALLOWED,
                "message" => 'Method Not Allowed (Allow: {POST})'
            ], Response::HTTP_METHOD_NOT_ALLOWED);
        }

        if (!$this->isGranted('ROLE__USER')) {
            return $this->json([
                "code" => Response::HTTP_UNAUTHORIZED,
                "message" => 'Unauthorized'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $userToken = $this->em->getRepository(UserToken::class);

        // Remove all refresh token of the User when logout
        if ($refreshs = count($userToken->getAllByUser($this->getUser())) !== 0) {
            foreach ($userToken->getAllByUser($this->getUser()) as $token) {
                $this->em->remove($token);
                $this->em->flush();
            }
        }

        // Tcheck if it's json contentType
        if (!empty($request->headers->get('content_type'))) {
            if (!\in_array($request->headers->get('content_type'), self::CONTENT_TYPE, true)) {

                return $this->json([
                    "code" => Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                    "message" => 'Invalid content type Header (Allow: {application/json & application/ld+json})'
                ], Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
            }
        }

        // Do something to clear user information or logging

        // @Todo : translation
        return $this->json(["code" => Response::HTTP_OK, "message" => 'Succefully logout'], Response::HTTP_OK);
    }
}
