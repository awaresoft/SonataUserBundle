<?php

namespace Awaresoft\Sonata\UserBundle\DataFixtures\ORM;

use Awaresoft\DoctrineBundle\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use FOS\UserBundle\Model\UserManagerInterface;

/**
 * Class LoadPageData
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 *
 */
class LoadUserDevData extends AbstractFixture
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * {@inheritDoc}
     */
    public static function getGroups(): array
    {
        return ['dev'];
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }

    public function load(ObjectManager $manager)
    {
        foreach (range(1, 20) as $id) {
            $user = $this->userManager->createUser();
            $user->setUsername($this->fakerGenerator->userName . $id);
            $user->setEmail($this->fakerGenerator->safeEmail);
            $user->setPlainPassword($this->fakerGenerator->randomNumber());
            $user->setEnabled(true);

            $this->userManager->updateUser($user);
        }

        $user = $this->userManager->createUser();
        $user->setUsername('johndoe');
        $user->setEmail($this->fakerGenerator->safeEmail);
        $user->setPlainPassword('johndoe');
        $user->setEnabled(true);
        $user->setSuperAdmin(false);

        $this->setReference('user-johndoe', $user);

        $manager->updateUser($user);

        // Behat testing purpose
        $user = $this->userManager->createUser();
        $user->setUsername('behat_user');
        $user->setEmail($this->fakerGenerator->safeEmail);
        $user->setEnabled(true);
        $user->setPlainPassword('behat_user');

        $this->userManager->updateUser($user);
    }

    /**
     * @param UserManagerInterface $userManager
     */
    public function setUserManager(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }
}
