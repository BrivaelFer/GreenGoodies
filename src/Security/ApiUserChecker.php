<?php
// src/Security/ApiUserChecker.php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ApiUserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        $this->checkApiAccess($user);
    }

    public function checkPostAuth(UserInterface $user): void
    {
        $this->checkApiAccess($user);
    }

    private function checkApiAccess(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if (!$user->isApiEnable()) {
            throw new CustomUserMessageAuthenticationException(
                'Votre accès API est désactivé.', code: 403
            );
        }
    }
}