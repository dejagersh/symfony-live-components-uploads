<?php

namespace App\Controller;

use App\Form\FilepondType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DemoController extends AbstractController
{
    #[Route('/')]
    public function index(): Response
    {
        return $this->render('demos/index.html.twig');
    }

    #[Route('/simple-live-component', 'simple_live_component')]
    public function simpleLiveComponent(): Response
    {
        return $this->render('demos/simple_live_component.html.twig');
    }

    #[Route('/simple-form', 'simple_form', methods: ['GET', 'POST'])]
    public function simpleForm(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('file', FilepondType::class)
            ->add('save', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dd($form->get('file')->getData());
        }

        return $this->render('demos/simple_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/live-form', 'live_form', methods: ['GET', 'POST'])]
    public function liveForm(): Response
    {
        return $this->render('demos/live_form.html.twig');
    }
}