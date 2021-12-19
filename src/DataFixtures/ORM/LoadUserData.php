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
class LoadUserData extends AbstractFixture
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
        return ['prod', 'dev'];
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
        $user = $this->userManager->createUser();
        $user->setUsername('super_admin');
        $user->setEmail($this->fakerGenerator->safeEmail);
        $user->setPlainPassword('super_admin');
        $user->setEnabled(true);
        $user->setSuperAdmin(true);

        $this->userManager->updateUser($user);

        $user = $this->userManager->createUser();
        $user->setUsername('secure_admin');
        $user->setEmail($this->fakerGenerator->safeEmail);
        $user->setPlainPassword('secure_admin');
        $user->setEnabled(true);
        $user->setSuperAdmin(true);
        // google chart qr code : https://www.google.com/chart?chs=200x200&chld=M|0&cht=qr&chl=otpauth://totp/secure@http://demo.sonata-project.org%3Fsecret%3D4YU4QGYPB63HDN2C
        $user->setTwoStepVerificationCode('4YU4QGYPB63HDN2C');

        $this->userManager->updateUser($user);

        $this->addReference('user-admin', $user);
    }

    /**
     * @param UserManagerInterface $userManager
     */
    public function setUserManager(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }
}
