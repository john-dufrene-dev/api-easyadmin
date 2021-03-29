<?php

namespace App\Controller\Api\Auth;

use App\Entity\Customer\User;
use Symfony\Component\Uid\Ulid;
use App\Entity\Customer\UserToken;
use App\Service\Api\Email\UserMailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @Route("/api")
 */
class RegistrationController extends AbstractController
{
    public const CONTENT_TYPE = ['application/json', 'application/ld+json'];

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
     * @Route("/auth/register", name="api_register")
     * 
     * @param  void
     * @return JsonResponse
     */
    public function registerApi(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        ValidatorInterface $validator,
        JWTTokenManagerInterface $JWTManager,
        RefreshTokenManagerInterface $refreshTokenManager
    ): JsonResponse {

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        // Tcheck if POST Method
        if (!$request->isMethod('POST')) {

            return $this->json([
                "code" => Response::HTTP_METHOD_NOT_ALLOWED,
                "message" => 'Method Not Allowed (Allow: {POST})'
            ], Response::HTTP_METHOD_NOT_ALLOWED);
        }

        // Tcheck if it's json contentType
        if (!\in_array($request->headers->get('content_type'), self::CONTENT_TYPE, true)) {

            return $this->json([
                "code" => Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                "message" => 'Invalid content type Header (Allow: {application/json & application/ld+json})'
            ], Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        }

        $content = $serializer->deserialize($request->getContent(), User::class, 'json');

        $username = $content->getEmail() ? $content->getEmail() : null;
        $password = $content->getPassword() ? $content->getPassword() : null;

        $datetime = new \DateTime();
        $datetime->modify('+' . $this->params->get('gesdinet_jwt_refresh_token.ttl') . ' seconds');

        // Tcheck if username or password are null
        if (null === $username || null === $password || !isset($username) || !isset($password)) {

            return $this->json([
                "code" => Response::HTTP_BAD_REQUEST,
                "message" => 'Bad Request'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Create The User Entity
        $user = new User();
        $user->setEmail($username);
        $user->setPlainPassword($password);
        $user->setRoles(['ROLE__USER']);

        // Create the refresh token User
        $refreshToken = $refreshTokenManager->create();
        $refreshToken->setUsername($user->getUsername());
        $refreshToken->setRefreshToken();
        $refreshToken->setValid($datetime);

        $errors_user = $validator->validate($user);
        $errors_token = $validator->validate($user);

        // Tcheck validations constraints
        if (0 === count($errors_user) && 0 === count($errors_token)) {

            $user->setPassword($passwordEncoder->encodePassword($user, $password));

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
                    $this->mailer->sendEmailConfirmationApi(
                        'api_verify_email',
                        $user,
                        $this->translator->trans('email.email_confirmation.header', [], 'email')
                    );
                }
            }

            $refreshTokenManager->save($refreshToken);

            return $this->json([
                'token' => $JWTManager->create($user),
                'refresh_token' => $refreshToken->getRefreshToken()
            ], Response::HTTP_OK);
        } else {

            $errs = [];
            foreach ($errors_user as $error) {
                $errs = array_merge($errs, [$error->getMessage()]);
            }

            foreach ($errors_token as $error) {
                $errs = array_merge($errs, [$error->getMessage()]);
            }

            return $this->json([
                "code" => Response::HTTP_BAD_REQUEST,
                "message" => array_values(array_unique($errs))
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/auth/logout", name="api_logout")
     * 
     * @param  mixed $request
     * @return JsonResponse
     */
    public function logoutApi(
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
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

        $userToken = $em->getRepository(UserToken::class);

        // Remove all refresh token of the User when logout
        if ($refreshs = count($userToken->getAllByUser($this->getUser())) !== 0) {
            foreach ($userToken->getAllByUser($this->getUser()) as $token) {
                $em->remove($token);
                $em->flush();
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

    /**
     * @Route("/auth/verify/email", name="api_verify_email")
     * 
     * @param  mixed $request
     * @return JsonResponse
     */
    public function verifyUserEmailApi(Request $request, JWTTokenManagerInterface $JWTManager): Response
    {
        // @Todo : Add JWT Token to auth with query parameter

        $ulid = new Ulid();

        // If active_confirm_user is false return 404 Not Found Page
        if (!$this->params->get('active_confirm_user')) {
            throw new NotFoundHttpException('Page doesn\'t exist');
        }

        $uuid = (null !== $request->query->get('uuid')) ? $request->query->get('uuid') : null;

        if (null === $uuid || !isset($uuid)) {
            // @Todo : translation
            throw new BadRequestException("Uuid and email is required !");
        }

        $user = $this->em->getRepository(User::class)->findOneBy(['uuid' => $uuid]);

        if (!$user) {
            // @Todo : translation
            throw new NotFoundHttpException('User doesn\'t exist !');
        }

        $token = $JWTManager->create($user);

        if (!$token) {
            // @Todo : translation
            throw new BadRequestException('Jwt Token is invalid !');
        }

        // $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->mailer->handleEmailConfirmationApi($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {

            $this->addFlash('admin_default_flashes', [
                'message' => $exception->getReason(),
                'class' => 'danger'
            ]);
            return $this->redirectToRoute('admin_callback', ['token' => $ulid->toBase32()]);
        }

        // @todo : send email activation Account

        // @Todo : translation
        $this->addFlash('admin_callback_flashes', [
            'message' => 'Your e-mail address has been verified',
            'class' => 'success'
        ]);
        return $this->redirectToRoute('admin_callback', ['token' => $ulid->toBase32()]);
    }
}
