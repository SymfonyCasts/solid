<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Form\AgreeToUpdatedTermsFormType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Security;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupInterface;
use Twig\Environment;

class AgreeToTermsSubscriber implements EventSubscriberInterface
{
    private $security;
    private $formFactory;
    private $twig;
    private $entrypointLookup;

    public function __construct(Security $security, FormFactoryInterface $formFactory, Environment $twig, EntrypointLookupInterface $entrypointLookup)
    {
        $this->security = $security;
        $this->formFactory = $formFactory;
        $this->twig = $twig;
        $this->entrypointLookup = $entrypointLookup;
    }

    public function onRequestEvent(RequestEvent $event)
    {
        $user = $this->security->getUser();

        // only need this for authenticated users
        if (!$user instanceof User) {
            return;
        }

        // in reality, you would hardcode the most recent "terms" date
        // change so you can see if the user needs to "re-agree". I've
        // set it dynamically to 1 year ago to avoid anyone hitting
        // this - as it's just example code...
        //$latestTermsDate = new \DateTimeImmutable('2019-10-15');
        $latestTermsDate = new \DateTimeImmutable('-1 year');

        $form = $this->formFactory->create(AgreeToUpdatedTermsFormType::class);

        $html = $this->twig->render('main/agreeUpdatedTerms.html.twig', [
            'form' => $form->createView()
        ]);
        // resets Encore assets so they render correctly later
        // only technically needed here because we should really
        // "exit" this function before rendering the template if
        // we know the user doesn't need to see the form!
        $this->entrypointLookup->reset();

        // user is up-to-date!
        if ($user->getAgreedToTermsAt() >= $latestTermsDate) {
            return;
        }

        $response = new Response($html);
        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return [
            RequestEvent::class => 'onRequestEvent',
        ];
    }
}
