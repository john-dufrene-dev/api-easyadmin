<?php

namespace App\Controller\Admin\Security;

use App\Entity\Security\Admin;
use App\Service\Admin\Email\AdminMailer;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Type\Security\ChangePasswordFormType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Form\Type\Security\ResetPasswordRequestFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;

#[Route("%url_for_admin%/reset-password")]
class SecurityResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    private $translator;

    private $resetPasswordHelper;

    private $params;

    private $managerRegistry;

    public function __construct(
        TranslatorInterface $translator,
        ParameterBagInterface $params,
        ResetPasswordHelperInterface $resetPasswordHelper,
        ManagerRegistry $managerRegistry
    ) {
        $this->translator = $translator;
        $this->resetPasswordHelper = $resetPasswordHelper;
        $this->params = $params;
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * Display & process form to request a password reset.
     */
    #[Route("", name: 'admin_forgot_password_request')]
    public function request(Request $request, AdminMailer $mailer): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processSendingPasswordResetEmail(
                $form->get('email')->getData(),
                $mailer
            );
        }

        return $this->render('admin/security/reset_password/request.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    /**
     * Confirmation page after a user has requested a password reset.
     */
    #[Route("/check-email", name: 'admin_check_email')]
    public function checkEmail(): Response
    {
        // We prevent users from directly accessing this page
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }

        return $this->render('admin/security/reset_password/check_email.html.twig', [
            'tokenLifetime' => $resetToken,
        ]);
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     */
    #[Route("/reset/{token}", name: 'admin_reset_password')]
    public function reset(Request $request, UserPasswordHasherInterface $passwordEncoder, string $token = null): Response
    {
        if ($token) {
            // We store the token in session and remove it from the URL, to avoid the URL being
            // loaded in a browser and potentially leaking the token to 3rd party JavaScript.
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('admin_reset_password');
        }

        $token = $this->getTokenFromSession();
        if (null === $token) {
            throw $this->createNotFoundException($this->translator->trans('reset.return.error.not_found', [], 'admin'));
        }

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('reset_password_error', sprintf(
                $this->translator->trans('reset.return.error.validating', [], 'admin')
                // . ' - %s ',
                // $e->getReason()
            ));

            return $this->redirectToRoute('admin_forgot_password_request');
        }

        // The token is valid; allow the user to change their password.
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // A password reset token should be used only once, remove it.
            $this->resetPasswordHelper->removeResetRequest($token);

            // Encode the plain password, and set it.
            $encodedPassword = $passwordEncoder->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setPassword($encodedPassword);
            $this->managerRegistry->getManager()->flush();

            // The session is cleaned up after the password has been changed.
            $this->cleanSessionAfterReset();

            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/security/reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }

    private function processSendingPasswordResetEmail(string $emailFormData, AdminMailer $mailer): RedirectResponse
    {
        $admin = $this->managerRegistry->getRepository(Admin::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        // Do not reveal whether a admin account was found or not.
        if (!$admin) {
            return $this->redirectToRoute('admin_check_email');
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($admin);
        } catch (ResetPasswordExceptionInterface $e) {
            // If you want to tell the admin why a reset email was not sent, uncomment
            // the lines below and change the redirect to 'admin_forgot_password_request'.
            // Caution: This may reveal if a admin is registered or not.
            //
            $this->addFlash('reset_password_error', sprintf(
                $this->translator->trans('reset.return.error.problem', [], 'admin')
                // . ' - %s ',
                // $e->getReason()
            ));

            return $this->redirectToRoute('admin_check_email');
        }

        if (
            true == $this->params->has('mailer_user')
            && $this->params->get('mailer_user') != 'domain@domain.com'
        ) {
            // Send email reset password to admin
            $mailer->ResetPassword(
                $admin,
                $resetToken,
                $this->resetPasswordHelper->getTokenLifetime(),
                $this->translator->trans('email.reset_password.header', [], 'email')
            );
        }

        return $this->redirectToRoute('admin_check_email');
    }
}
