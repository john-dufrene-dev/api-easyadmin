<?php

namespace App\Controller\Api\Auth\ResetPassword;

use App\Entity\Customer\User;
use App\Service\Api\Email\UserMailer;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Customer\UserResetPassword;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\Api\Builder\ApiResponseBuilder;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api")
 */
class SendResetPasswordController extends AbstractController
{
    /**
     * mailer
     *
     * @var mixed
     */
    private $mailer;

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
    public function __construct(UserMailer $mailer, EntityManagerInterface $em)
    {
        $this->mailer = $mailer;
        $this->em = $em;
    }

    /**
     * @Route("/auth/reset/send", name="api_reset_password_send")
     * 
     * @param  mixed $request
     * @return JsonResponse
     */
    public function sendResetPasswordSecretApi(Request $request, ApiResponseBuilder $apiResponseBuilder): JsonResponse
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        // Tcheck if POST Method
        if (!$request->isMethod('POST')) {
            return $apiResponseBuilder->CheckIfMethodPost();
        }

        // Tcheck if it's json contentType
        if (!\in_array($request->headers->get('content_type'), ApiResponseBuilder::CONTENT_TYPE, true)) {
            return $apiResponseBuilder->checkIfAcceptContentType();
        }

        // Tcheck if content is empty
        if (empty($request->getContent())) {
            return $apiResponseBuilder->checkIfBodyIsEmpty();
        }

        // Tcheck if email content exist
        if (!isset($request->toArray()['email'])) {
            return $apiResponseBuilder->checkIfBadQueryParameters();
        }

        $content = $serializer->deserialize($request->getContent(), User::class, 'json');
        $email = $content->getEmail() ? $content->getEmail() : null;

        // Tcheck if valid parameters
        if (null === $email || !isset($email)) {
            return $apiResponseBuilder->checkIfInvalidQueryParameters();
        }

        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

        // Tcheck if User exist
        if (!$user) {
            return $apiResponseBuilder->checkIfInvalidUser();
        }

        $userResetPassword = $this->em->getRepository(UserResetPassword::class);
        $reset_password_all = $userResetPassword->findAll();
        $user_reset_password_all = $userResetPassword->findBy(['user' => $user->getUuid()->toBinary()]);

        // Delete all expired secret
        foreach ($reset_password_all as $reset_password_one) {
            if ($reset_password_one->isExpired()) {
                $this->em->remove($reset_password_one);
            }
        }

        // If > 5 try all secret for the user are deleted
        if (count($user_reset_password_all) > 5) {

            foreach ($user_reset_password_all as $user_reset_password_one) {
                $this->em->remove($user_reset_password_one);
            }

            $this->em->flush();

            // Tcheck if too many try
            return $apiResponseBuilder->checkIfTooManyTry();
        }

        $reset_password = new UserResetPassword();
        $reset_password->setUser($user);

        $this->em->persist($reset_password);
        $this->em->flush();

        $this->mailer->sendResetPasswordSecretApi($user, $reset_password, UserResetPassword::ADITIONAL_TIME);

        return $this->json([
            "token" => $reset_password->getHashToken(),
        ], Response::HTTP_OK);
    }
}
