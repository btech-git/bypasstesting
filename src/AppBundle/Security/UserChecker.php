<?php

namespace AppBundle\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserChecker as BaseUserChecker;

class UserChecker extends BaseUserChecker
{
    public function checkPreAuth(UserInterface $user)
    {
        parent::checkPreAuth($user);

        if (!$user->getIsActive()) {
            throw new CustomUserMessageAuthenticationException('Your account is non-active.');
        }
    }
}
