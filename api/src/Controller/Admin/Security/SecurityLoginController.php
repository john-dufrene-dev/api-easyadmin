<?php

namespace App\Controller\Admin\Security;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/")
 */
class SecurityLoginController extends AbstractController
{
    protected $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @Route("/login", name="admin_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('admin_dashboard');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('admin/security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'translation_domain' => 'admin',
            'page_title' => 'EASY-ADMIN-API login',
            'csrf_token_intention' => 'authenticate',
            'target_path' => $this->generateUrl('admin_dashboard'),
            'username_label' => 'login.username_label',
            'password_label' => 'login.password_label',
            'sign_in_label' => 'login.log_in',
            'username_parameter' => 'email',
            'password_parameter' => 'password',
        ]);
    }

    /**
     * @Route("/logout", name="admin_logout")
     */
    public function logout()
    {
        throw new \LogicException($this->translator->trans('', [], 'admin'));
    }
}
