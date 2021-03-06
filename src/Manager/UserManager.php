<?php

namespace Awaresoft\Sonata\UserBundle\Manager;

use Sonata\UserBundle\Entity\UserManager as BaseUserManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class UserManager
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class UserManager extends BaseUserManager
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }
}
