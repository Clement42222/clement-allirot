<?php
// src/EventSubscriber/LoginSubscriber.php

namespace App\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginSubscriber implements EventSubscriberInterface
{
    private $logger;
    private $security;
    private $em;

    public function __construct(
        LoggerInterface $logger,
        Security $security,
        EntityManagerInterface $em
    ) {
        $this->logger = $logger;
        $this->security = $security;
        $this->em = $em;
    }

    public function onLogin()
    {
        // Récyupérer l'utilisateur connecté
        $user = $this->security->getUser();

        // Enregistrer un message de journalisation
        $this->logger->notice('L\'utilisateur ' . $user->getFirstname() . ' s\'est connecté.');

        // Code pour mettre à jour la date de dernière connexion de l'utilisateur
        $user->setLastLoginAt(new \DateTime());

        // Enregistrer les modifications dans la base de données
        $this->em->flush();
    }

    public static function getSubscribedEvents()
    {
        return [
            LoginSuccessEvent::class => 'onLogin',
        ];
    }
}
