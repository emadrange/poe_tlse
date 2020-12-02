<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyController extends AbstractController
{
    /**
     * @Route("/index", name="index", methods={"GET"})
     */
    public function index()
    {
        $response = new Response('Bienvenue sur mon premier contrôleur');

        return $response;
    }

    /**
     * @Route("/second", name="second_page", methods={"GET"})
     */
    public function secondPage()
    {
        return $this->render('my/second.html.twig');
    }
}