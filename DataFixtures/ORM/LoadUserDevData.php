<?php

namespace Awaresoft\Sonata\UserBundle\DataFixtures\ORM;

use Awaresoft\Doctrine\Common\DataFixtures\AbstractFixture as AwaresoftAbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class LoadPageData
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 *
 */
class LoadUserDevData extends AwaresoftAbstractFixture
{
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }

    /**
     * {@inheritDoc}
     */
    public function getEnvironments()
    {
        return array('dev');
    }

    public function doLoad(ObjectManager $manager)
    {
        $manager = $this->getUserManager();
        $faker = $this->getFaker();

        foreach (range(1, 20) as $id) {
            $user = $manager->createUser();
            $user->setUsername($faker->userName . $id);
            $user->setEmail($faker->safeEmail);
            $user->setPlainPassword($faker->randomNumber());
            $user->setEnabled(true);
            $user->setLocked(false);

            $manager->updateUser($user);
        }

        $user = $manager->createUser();
        $user->setUsername('johndoe');
        $user->setEmail($faker->safeEmail);
        $user->setPlainPassword('johndoe');
        $user->setEnabled(true);
        $user->setSuperAdmin(false);
        $user->setLocked(false);

        $this->setReference('user-johndoe', $user);

        $manager->updateUser($user);

        // Behat testing purpose
        $user = $manager->createUser();
        $user->setUsername('behat_user');
        $user->setEmail($faker->safeEmail);
        $user->setEnabled(true);
        $user->setPlainPassword('behat_user');

        $manager->updateUser($user);
    }

    /**
     * @return \FOS\UserBundle\Model\UserManagerInterface
     */
    public function getUserManager()
    {
        return $this->container->get('fos_user.user_manager');
    }
}
