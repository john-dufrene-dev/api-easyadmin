<?php

namespace App\Controller\Admin;

use Symfony\Component\Uid\Ulid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DefaultController extends AbstractController
{
    #[Route("/", name: 'admin_default')]
    public function index(): Response
    {
        return $this->render('admin/pages/default.html.twig');
    }

    #[Route("/callback", name: 'admin_callback')]
    public function callback(Request $request): Response
    {
        // You must define flash session to render the correct page
        if (!$request->get('token')) {
            throw new NotFoundHttpException('Page Not Found !');
        }

        // Verify if it's a valid Ulid()
        if (!$isValid = Ulid::isValid($request->get('token'))) {
            throw new NotFoundHttpException('Invalid Ulid !');
        }

        return $this->render('admin/pages/callback.html.twig');
    }
}
