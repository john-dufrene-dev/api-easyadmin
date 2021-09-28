<?php

namespace App\Controller\Admin\Security;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route("%url_for_admin%")]
class SecurityLogoutController extends AbstractController
{
    protected $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    #[Route("/logout", name: 'admin_logout')]
    public function logout(): Response
    {
        throw new \LogicException($this->translator->trans('', [], 'admin'));
    }
}
