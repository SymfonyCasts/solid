<?php

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResendConfirmationController extends AbstractController
{
    /**
     * @Route("/resend-confirmation", methods={"POST"})
     */
    public function resend(Request $request, UserRepository $userRepository)
    {
        $email = $request->request->get('email');

        $user = $userRepository->findOneBy(['email' => $email]);
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        // TODO: send confirmation email

        return new Response(null, 204);
    }
}
