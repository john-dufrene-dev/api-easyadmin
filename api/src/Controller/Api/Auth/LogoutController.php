<?php

namespace App\Controller\Api\Auth;

use App\Entity\Customer\UserToken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\Api\Builder\ApiResponseBuilder;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api")
 */
class LogoutController extends AbstractController
{
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
    public function logoutApi(Request $request, ApiResponseBuilder $apiResponseBuilder): JsonResponse
    {
        // Tcheck if POST Method
        if (!$request->isMethod('POST')) {
            return $apiResponseBuilder->CheckIfMethodPost();
        }

        // Tcheck if it's json contentType
        if (!\in_array($request->headers->get('content_type'), ApiResponseBuilder::CONTENT_TYPE, true)) {
            return $apiResponseBuilder->checkIfAcceptContentType();
        }

        // Tcheck if user is granted
        if (!$this->isGranted('ROLE__USER')) {
            return $apiResponseBuilder->checkIfUnauthorized();
        }

        $userToken = $this->em->getRepository(UserToken::class);

        // Remove all refresh token of the User when logout
        if ($refreshs = count($userToken->getAllByUser($this->getUser())) !== 0) {
            foreach ($userToken->getAllByUser($this->getUser()) as $token) {
                $this->em->remove($token);
                $this->em->flush();
            }
        }

        // Do something to clear user information or logging

        // @Todo : translation
        return $this->json(["code" => Response::HTTP_OK, "message" => 'Succefully logout'], Response::HTTP_OK);
    }
}
