<?php

namespace App\Controller\Api\Auth\ResetPassword;

use Symfony\Component\Uid\Ulid;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Customer\UserResetPassword;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\Api\Builder\ApiResponseBuilder;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

/**
 * @Route("/api")
 */
class CheckResetPasswordController extends AbstractController
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
     * @Route("/auth/reset/check", name="api_reset_password_check")
     * 
     * @param  mixed $request
     * @return JsonResponse
     */
    public function checkResetPasswordSecretApi(
        Request $request,
        ApiResponseBuilder $apiResponseBuilder,
        JWTTokenManagerInterface $JWTManager
    ): JsonResponse {

        // Check if POST Method
        if (!$request->isMethod('POST')) {
            return $apiResponseBuilder->CheckIfMethodPost();
        }

        // Check if it's json contentType
        if (!\in_array($request->headers->get('content_type'), ApiResponseBuilder::CONTENT_TYPE, true)) {
            return $apiResponseBuilder->checkIfAcceptContentType();
        }

        // Check if content is empty
        if (empty($request->getContent())) {
            return $apiResponseBuilder->checkIfBodyIsEmpty();
        }

        if (!isset($request->toArray()['secret']) || !isset($request->toArray()['token'])) {
            return $apiResponseBuilder->checkIfBadQueryParameters();
        }

        $hash_token = (null !== $request->toArray()['token']) ? $request->toArray()['token'] : null;
        $secret = (null !== $request->toArray()['secret']) ? $request->toArray()['secret'] : null;

        if (null === $hash_token || !isset($hash_token) || null === $secret || !isset($secret)) {
            return $apiResponseBuilder->checkIfInvalidQueryParameters();
        }

        // Check signature of the Ulid
        $isValid = Ulid::isValid($hash_token);

        if (!$isValid) {
            return $apiResponseBuilder->checkIfValidUlid();
        }

        $userResetPassword = $this->em->getRepository(UserResetPassword::class);
        $user_identifier = $userResetPassword->findOneBy([
            'secret' => $secret,
            'hash_token' => $hash_token,
        ]);

        // Check if User exist
        if (!$user_identifier) {
            return $apiResponseBuilder->checkIfInvalidUser();
        }

        $user = $user_identifier->getUser();

        // Check if token is expired
        if ($user_identifier->isExpired()) {
            $this->em->remove($user_identifier);
            $this->em->flush();
            return $apiResponseBuilder->checkIfExpiredToken();
        }

        $token = $JWTManager->create($user);

        // Check if token is valid
        if (!$token) {
            return $apiResponseBuilder->checkIfInvalidToken();
        }

        // User secret and token is just valid one time
        $this->em->remove($user_identifier);
        $this->em->flush();

        return $this->json([
            "token" => $token
        ], Response::HTTP_OK);
    }
}
