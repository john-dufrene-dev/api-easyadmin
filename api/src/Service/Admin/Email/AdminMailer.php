<?php

namespace App\Service\Admin\Email;

use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * AdminMailer
 */
class AdminMailer
{
    /**
     * mailer
     *
     * @var mixed
     */
    protected $mailer;

    /**
     * params
     *
     * @var mixed
     */
    protected $params;

    /**
     * em
     *
     * @var mixed
     */
    protected $em;

    /**
     * __construct
     *
     * @param  mixed $mailer
     * @param  mixed $params
     * @param  mixed $em
     * @return void
     */
    public function __construct(MailerInterface $mailer, ParameterBagInterface $params, EntityManagerInterface $em)
    {
        $this->mailer = $mailer;
        $this->params = $params;
        $this->em = $em;
    }

    /**
     * EmailResetPassword
     *
     * @return void
     */
    public function ResetPassword(
        $user,
        $token,
        $lifetime,
        $subject = 'Default password reset request', // Default message without transllation, you can change it
        $template = 'email/admin/security/reset_password.html.twig'
    ) {
        $email = (new TemplatedEmail())
            ->from(new Address($this->params->get('mailer_user'), $subject))
            ->to($user->getEmail())
            ->subject($subject)
            ->htmlTemplate($template)
            ->context([
                'resetToken' => $token,
                'tokenLifetime' => $lifetime,
            ]);

        $this->mailer->send($email);
    }
}
