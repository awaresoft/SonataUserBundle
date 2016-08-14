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
class LoadUserData extends AwaresoftAbstractFixture
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
        return array('dev', 'prod');
    }

    public function doLoad(ObjectManager $manager)
    {
        $manager = $this->getUserManager();
        $faker = $this->getFaker();

        $user = $manager->createUser();
        $user->setUsername('admin');
        $user->setEmail($faker->safeEmail);
        $user->setPlainPassword('admin');
        $user->setEnabled(true);
        $user->setSuperAdmin(true);
        $user->setLocked(false);

        $manager->updateUser($user);

        $user = $manager->createUser();
        $user->setUsername('secure');
        $user->setEmail($faker->safeEmail);
        $user->setPlainPassword('secure');
        $user->setEnabled(true);
        $user->setSuperAdmin(true);
        $user->setLocked(false);
        // google chart qr code : https://www.google.com/chart?chs=200x200&chld=M|0&cht=qr&chl=otpauth://totp/secure@http://demo.sonata-project.org%3Fsecret%3D4YU4QGYPB63HDN2C
        $user->setTwoStepVerificationCode('4YU4QGYPB63HDN2C');

        $manager->updateUser($user);

        $this->addReference('user-admin', $user);
    }

    /**
     * @return \FOS\UserBundle\Model\UserManagerInterface
     */
    public function getUserManager()
    {
        return $this->container->get('fos_user.user_manager');
    }
}
