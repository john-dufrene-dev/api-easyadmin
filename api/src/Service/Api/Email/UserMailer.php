<?php

namespace App\Service\Api\Email;

use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class UserMailer
{
    /**
     * verifyEmailHelper
     *
     * @var mixed
     */
    private $verifyEmailHelper;

    /**
     * mailer
     *
     * @var mixed
     */
    private $mailer;

    /**
     * entityManager
     *
     * @var mixed
     */
    private $entityManager;

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
        VerifyEmailHelperInterface $helper,
        MailerInterface $mailer,
        EntityManagerInterface $manager,
        ParameterBagInterface $params
    ) {
        $this->verifyEmailHelper = $helper;
        $this->mailer = $mailer;
        $this->entityManager = $manager;
        $this->params = $params;
    }

    /**
     * RegistationApi
     *
     * @return void
     */
    public function sendRegistationApi(
        $user,
        $subject = 'Default registration User', // Default message without translation, you can change it
        $template = 'email/api/auth/register.html.twig'
    ) {
        $email = (new TemplatedEmail())
            ->from(new Address($this->params->get('mailer_user'), $subject))
            ->to($user->getEmail())
            ->subject($subject)
            ->htmlTemplate($template)
            ->context(['user' => $user]);

        $this->mailer->send($email);
    }

    /**
     * sendEmailConfirmation
     *
     * @return void
     */
    public function sendEmailConfirmationApi(
        string $verifyEmailRouteName,
        UserInterface $user,
        $subject = 'Default confirm email User', // Default message without translation, you can change it
        $template = 'email/api/auth/confirmation_email.html.twig'
    ): void {
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            $verifyEmailRouteName,
            $user->getUuid(),
            $user->getEmail(),
            ['uuid' => $user->getUuid()->toRfc4122()],
        );

        $email = (new TemplatedEmail())
            ->from(new Address($this->params->get('mailer_user'), $subject))
            ->to($user->getEmail())
            ->subject($subject)
            ->htmlTemplate($template);

        $context = $email->getContext();
        $context['signedUrl'] = $signatureComponents->getSignedUrl();
        // @Todo : Change URL for frond-end and json format
        $context['expiresAt'] = $signatureComponents->getExpiresAt();

        $email->context($context);

        $this->mailer->send($email);
    }

    /**
     * handleEmailConfirmation
     *
     * @param  mixed $request
     * @param  mixed $user
     * @return void
     * @throws VerifyEmailExceptionInterface
     */
    public function handleEmailConfirmationApi(Request $request, UserInterface $user): void
    {
        $this->verifyEmailHelper->validateEmailConfirmation($request->getUri(), $user->getUuid(), $user->getEmail());

        $user->setIsVerified(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * RegistationApi
     *
     * @return void
     */
    public function sendResetPasswordSecretApi(
        $user,
        $reset,
        $minutes = 10,
        $subject = 'Default reset password secret User', // Default message without translation, you can change it
        $template = 'email/api/auth/reset_password/reset_password_secret.html.twig'
    ) {
        $email = (new TemplatedEmail())
            ->from(new Address($this->params->get('mailer_user'), $subject))
            ->to($user->getEmail())
            ->subject($subject)
            ->htmlTemplate($template)
            ->context([
                'user' => $user,
                'reset' => $reset,
                'minutes' => $minutes,
            ]);

        $this->mailer->send($email);
    }
}
