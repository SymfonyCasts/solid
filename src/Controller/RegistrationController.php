<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/signup", name="signup")
     */
    public function signup(Request $request, UserManager $userManager)
    {
        $form = $this->createForm(RegistrationFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var $user User */
            $user = $form->getData();
            $user->setUsername($user->getEmail());
            $user->setAgreedToTermsAt(new \DateTime('now'));

            $plainPassword = $form->get('plainPassword')->getData();

            $userManager->create($user, $plainPassword);

            $this->addFlash('success', 'User created successfully!');

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
