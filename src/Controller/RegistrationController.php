<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Manager\UserManager;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
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

            $userManager->register($user, $plainPassword);

            $this->addFlash('success', 'Fist Pump! Let\'s go find some Sasquatch!');

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/confirm/{token}", name="check_confirmation_link")
     */
    public function confirmAction(string $token, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $user = $userRepository->findOneBy(['confirmationToken' => $token]);

        if (!$user) {
            throw $this->createNotFoundException(sprintf('The user with confirmation token "%s" does not exist', $token));
        }

        $user->setConfirmationToken(null);

        $entityManager->flush();

        $this->addFlash('success', 'The email was successfully confirmed');

        return $this->redirectToRoute('app_homepage');
    }
}
