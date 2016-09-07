<?php

namespace Awaresoft\Sonata\UserBundle\Command;

use Application\UserBundle\Entity\User;
use Awaresoft\Sonata\UserBundle\Entity\Group;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class JoinToUserGroupCommand
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class JoinToUserGroupCommand extends ContainerAwareCommand
{
    const LIMIT = 1000;

    /**
     * Configuration of command
     */
    protected function configure()
    {
        $this
            ->setName('awaresoft:user:join-to-user-group')
            ->setDescription('Join all registered users to user group');
    }

    /**
     * Execute command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userManager = $this->getContainer()->get('sonata.user.user_manager');
        $count = $userManager->getRepository()->countAll();

        $progress = new ProgressBar($output, $count);
        $progress->start();

        $group = $this->createUserGroup();
        $this->joinToUserGroup($group, $output, $progress);

        $progress->finish();
    }

    /**
     * Create group
     *
     * @return \FOS\UserBundle\Model\GroupInterface|mixed
     */
    protected function createUserGroup()
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $groupManager = $this->getContainer()->get('sonata.user.group_manager');

        if (!$group = $groupManager->findGroupByName('User')) {
            $group = $groupManager->createGroup('User');
            $group->addRole('ROLE_USER');
            $groupManager->updateGroup($group);
        }

        return $group;
    }

    /**
     * @param Group $group
     * @param OutputInterface $output
     * @param ProgressBar $progress
     * @param int $offset
     */
    protected function joinToUserGroup(Group $group, OutputInterface $output, ProgressBar $progress, $offset = 0)
    {
        /**
         * @var User[] $users
         */
        $em = $this->getContainer()->get('doctrine')->getManager();
        $userManager = $this->getContainer()->get('sonata.user.user_manager');
        $users = $userManager->getRepository()->findBy([], ['id' => 'ASC'], self::LIMIT, $offset);

        if (!count($users)) {
            return;
        }

        foreach ($users as $user) {
            $user->addGroup($group);
            $userManager->updateUser($user, false);
            $progress->advance();
        }

        $em->flush();

        return $this->joinToUserGroup($group, $output, $progress, $offset + self::LIMIT);
    }
}
