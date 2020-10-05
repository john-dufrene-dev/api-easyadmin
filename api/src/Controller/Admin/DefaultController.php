<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="admin_default")
     */
    public function index()
    {
        return $this->render('admin/pages/default.html.twig');
    }
}
