<?php

namespace App\Controller\Api\Auth;

use App\Entity\Customer\User;
use App\Service\Api\Email\UserMailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class RegistrationController extends AbstractController
{
    public const CONTENT_TYPE = 'application/json';

    private $mailer;

    private $em;

    private $params;

    private $translator;

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
     * @Route("/api/auth/register", name="api_register")
     */
    public function registerApi(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        ValidatorInterface $validator,
        JWTTokenManagerInterface $JWTManager
    ): JsonResponse {

        // Tcheck if POST Method
        if (!$request->isMethod('POST')) {

            $data = [
                "code" => Response::HTTP_METHOD_NOT_ALLOWED,
                "message" => 'Method Not Allowed (Allow: {POST})'
            ];

            return $this->json($data, $status = Response::HTTP_METHOD_NOT_ALLOWED);
        }

        // Tcheck if it's json contentType
        if (self::CONTENT_TYPE !== $request->headers->get('content_type')) {

            $data = [
                "code" => Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                "message" => 'Invalid content type Header (Allow: {POST}) ' . Response::HTTP_UNSUPPORTED_MEDIA_TYPE
            ];

            return $this->json($data, $status = Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        }

        $content = json_decode($request->getContent(), true);

        $username = isset($content['username']) ? $content['username'] : null;
        $password = isset($content['username']) ? $content['password'] : null;

        // Tcheck if username or password are null
        if (null === $username || null === $password || !isset($username) || !isset($password)) {

            $data = [
                "code" => Response::HTTP_BAD_REQUEST,
                "message" => 'Bad Request ' . Response::HTTP_BAD_REQUEST
            ];

            return $this->json($data, $status = Response::HTTP_BAD_REQUEST);
        }

        $user = new User();

        $user->setEmail($username);
        $user->setPlainPassword($password);
        $user->setRoles(['ROLE__USER']);

        $errors = $validator->validate($user);

        // Tcheck validations constraints
        if (0 === count($errors)) {

            $user->setPassword($passwordEncoder->encodePassword($user, $password));

            // Possible value enable/disable
            if ('disable' == $this->params->get('active_confirm_user')) {
                $user->setIsVerified(true);
            }

            $this->em->persist($user);
            $this->em->flush();

            if (
                true == $this->params->get('active_confirm_user')
                && $this->params->get('mailer_user') != 'domain@domain.com'
            ) {
                // Send email confirmation active User if active_confirm_user (.ENV) is egal to 'enable
                if ('enable' == $this->params->get('active_confirm_user')) {
                    $this->mailer->sendEmailConfirmationApi(
                        'api_verify_email',
                        $user,
                        $this->translator->trans('email.email_confirmation.header', [], 'email')
                    );
                }
            }

            return new JsonResponse(['token' => $JWTManager->create($user)]);
        } else {

            $errs = [];
            foreach ($errors as $error) {
                $errs = array_merge($errs, [$error->getMessage()]);
            }

            $data = [
                "code" => Response::HTTP_BAD_REQUEST,
                "message" => array_values(array_unique($errs))
            ];

            // Return Jwt token of the User
            return $this->json($data, $status = Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/api/auth/logout", name="api_logout")
     */
    public function logoutApi(Request $request): JsonResponse
    {
        // Tcheck if POST Method
        if (!$request->isMethod('POST')) {

            $data = [
                "code" => Response::HTTP_METHOD_NOT_ALLOWED,
                "message" => 'Method Not Allowed (Allow: {POST})'
            ];

            return $this->json($data, $status = Response::HTTP_METHOD_NOT_ALLOWED);
        }

        // Tcheck if it's json contentType
        if (self::CONTENT_TYPE !== $request->headers->get('content_type')) {

            $data = [
                "code" => Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                "message" => 'Invalid content type Header (Allow: {POST}) ' . Response::HTTP_UNSUPPORTED_MEDIA_TYPE
            ];

            return $this->json($data, $status = Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        }

        // Do something to clear user information or logging

        $data = [
            "code" => Response::HTTP_OK,
            "message" => 'Succefully logout'
        ];

        return $this->json($data, $status = Response::HTTP_OK);
    }

    /**
     * @Route("/api/auth/verify/email", name="api_verify_email")
     */
    public function verifyUserEmailApi(Request $request): Response
    {
        $uuid = (null !== $request->query->get('uuid')) ? $request->query->get('uuid') : null;

        if (null === $uuid || !isset($uuid)) {

            $data = [
                "code" => Response::HTTP_BAD_REQUEST,
                "message" => 'Bad Request ' . Response::HTTP_BAD_REQUEST
            ];

            return new JsonResponse($data, $status = Response::HTTP_BAD_REQUEST);
        }

        $user = $this->em->getRepository(User::class)->findOneBy(['uuid' => $uuid]);

        if (!$user) {

            $data = [
                "code" => Response::HTTP_BAD_REQUEST,
                "message" => 'Bad Request ' . Response::HTTP_BAD_REQUEST
            ];

            return new JsonResponse($data, $status = Response::HTTP_BAD_REQUEST);
        }

        // $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->mailer->handleEmailConfirmationApi($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {

            $data = [
                "code" => Response::HTTP_BAD_REQUEST,
                "message" => 'Bad Request ' . $exception->getReason()
            ];

            return new JsonResponse($data, $status = Response::HTTP_BAD_REQUEST);
        }

        $data = [
            "code" => Response::HTTP_OK,
            "message" => 'Success'
        ];

        return new JsonResponse($data, $status = Response::HTTP_OK);
    }
}
