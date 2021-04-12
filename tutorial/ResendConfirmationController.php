<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResendConfirmationController extends AbstractController
{
    /**
     * @Route("/resend-confirmation", methods={"POST"})
     */
    public function resend()
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();

        // TODO: send confirmation email

        return new Response(null, 204);
    }
}
