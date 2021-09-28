<?php

namespace App\Controller\Api\Auth;

use App\Entity\Customer\User;
use App\Entity\Customer\UserInfo;
use App\Service\Api\Email\UserMailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\Api\Builder\ApiResponseBuilder;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[Route("/api")]
class RegistrationController extends AbstractController
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
     * params
     *
     * @var mixed
     */
    private $params;

    /**
     * translator
     *
     * @var mixed
     */
    private $translator;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        UserMailer $mailer,
        EntityManagerInterface $em,
        ParameterBagInterface $params,
        TranslatorInterface $translator
    ) {
        $this->mailer = $mailer;
        $this->em = $em;
        $this->params = $params;
        $this->translator = $translator;
    }

    /**
     * @param  void
     * @return JsonResponse
     */
    #[Route("/auth/register", name: 'api_register')]
    public function registerApi(
        Request $request,
        UserPasswordHasherInterface $passwordEncoder,
        ValidatorInterface $validator,
        JWTTokenManagerInterface $JWTManager,
        RefreshTokenManagerInterface $refreshTokenManager,
        ApiResponseBuilder $apiResponseBuilder
    ): JsonResponse {

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

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

        $content = $serializer->deserialize($request->getContent(), User::class, 'json');

        $username = $content->getEmail() ? $content->getEmail() : null;
        $password = $content->getPassword() ? $content->getPassword() : null;

        $datetime = new \DateTime();
        $datetime->modify('+' . $this->params->get('gesdinet_jwt_refresh_token.ttl') . ' seconds');

        // Check if username or password are null
        if (null === $username || null === $password || !isset($username) || !isset($password)) {
            return $apiResponseBuilder->checkIfBadRequest();
        }

        // Create The User Entity
        $user = new User();
        $user_info = new UserInfo();

        $user_info->setUser($user);
        $user->setEmail($username);
        $user->setPlainPassword($password);
        $user->setRoles(['ROLE__USER']);

        // @todo : add User informations
        $user->setUserInfo($user_info);

        $errors_user = $validator->validate($user);
        $errors_token = $validator->validate($user);

        // Check validations constraints
        if (0 === count($errors_user) && 0 === count($errors_token)) {

            $user->setPassword($passwordEncoder->hashPassword($user, $password));

            // If false the User is already verified
            if (!$this->params->get('active_confirm_user')) {
                $user->setIsVerified(true);
            }

            $this->em->persist($user);
            $this->em->flush();

            if (
                true == $this->params->get('mailer_user')
                && $this->params->get('mailer_user') != 'domain@domain.com'
            ) {

                // Send email registration User
                $this->mailer->sendRegistationApi(
                    $user,
                    $this->translator->trans('email.register.header', [], 'email') . $user->getEmail()
                );

                // Send email confirmation active User if active_confirm_user is true
                if ($this->params->get('active_confirm_user')) {
                    $user->setIsVerified(false);
                    $this->mailer->sendEmailConfirmationApi(
                        'api_verify_email',
                        $user,
                        $this->translator->trans('email.email_confirmation.header', [], 'email')
                    );
                }
            }

            // Create the refresh token User
            $refreshToken = $refreshTokenManager->create();
            $refreshToken->setUsername($user->getUserIdentifier());
            $refreshToken->setRefreshToken();
            $refreshToken->setValid($datetime);

            $refreshTokenManager->save($refreshToken);

            return $this->json([
                'token' => $JWTManager->create($user),
                'refresh_token' => $refreshToken->getRefreshToken()
            ], Response::HTTP_OK);
        }
        
        $errs = [];
        foreach ($errors_user as $error) {
            $errs = array_merge($errs, [$error->getMessage()]);
        }

        foreach ($errors_token as $error) {
            $errs = array_merge($errs, [$error->getMessage()]);
        }

        return $apiResponseBuilder->checkIfErrorsBadRequest($errs);
    }
}
