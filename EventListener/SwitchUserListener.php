<?php

namespace Awaresoft\Sonata\UserBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Http\Event\SwitchUserEvent;

/**
 * Class SwitchUserListener
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class SwitchUserListener
{
    /**
     * @var TokenStorage
     */
    protected $tokenStorage;

    /**
     * @param SwitchUserEvent $event
     */
    public function onSwitchUser(SwitchUserEvent $event)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $targetUserRoles = $event->getTargetUser()->getRoles();

        if ($event->getRequest()->get('_switch_user') === '_exit') {
            return;
        }

        if (in_array("ROLE_SUPER_ADMIN", $user->getRoles())) {
            return;
        }

        if (in_array("ROLE_SUPER_ADMIN", $targetUserRoles)) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Can not change role to ROLE_SUPER_ADMIN');
        }
    }

    /**
     * @param TokenStorage $tokenStorage
     */
    public function setToken(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }
}