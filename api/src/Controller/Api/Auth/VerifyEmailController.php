<?php

namespace App\Controller\Api\Auth;

use App\Entity\Customer\User;
use Symfony\Component\Uid\Ulid;
use App\Service\Api\Email\UserMailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @Route("/api")
 */
class VerifyEmailController extends AbstractController
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
     * __construct
     *
     * @return void
     */
    public function __construct(
        UserMailer $mailer,
        EntityManagerInterface $em,
        ParameterBagInterface $params
    ) {
        $this->mailer = $mailer;
        $this->em = $em;
        $this->params = $params;
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
            // @Todo : Transform in json response
            throw new NotFoundHttpException('Page doesn\'t exist');
        }

        $uuid = (null !== $request->query->get('uuid')) ? $request->query->get('uuid') : null;

        if (null === $uuid || !isset($uuid)) {
            // @Todo : translation
            // @Todo : Transform in json response
            throw new BadRequestException("Uuid and email is required !");
        }

        $user = $this->em->getRepository(User::class)->findOneBy(['uuid' => $uuid]);

        if (!$user) {
            // @Todo : translation
            // @Todo : Transform in json response
            throw new NotFoundHttpException('User doesn\'t exist !');
        }

        $token = $JWTManager->create($user);

        if (!$token) {
            // @Todo : translation
            // @Todo : Transform in json response
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
            // @Todo : Transform in json response
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
