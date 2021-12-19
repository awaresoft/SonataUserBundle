<?php

namespace Awaresoft\Sonata\UserBundle\Admin\Manager;

use Awaresoft\Sonata\UserBundle\Manager\UserManager;
use Doctrine\DBAL\DBALException;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class AdminUserManager
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class AdminUserManager extends ModelManager
{
    protected $manager;
    protected $deleteFlush = true;

    /**
     * @param \Symfony\Bridge\Doctrine\RegistryInterface $doctrine
     * @param UserManager $manager
     */
    public function __construct(RegistryInterface $doctrine, $manager)
    {
        parent::__construct($doctrine);

        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function create($object)
    {
        try {
            $this->manager->update($object);
        } catch (DBALException $e) {
            throw new ModelManagerException('', 0, $e);
        } catch (\PDOException $e) {
            throw new ModelManagerException('', 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function update($object)
    {
        try {
            $this->manager->update($object);
        } catch (DBALException $e) {
            throw new ModelManagerException('', 0, $e);
        } catch (\PDOException $e) {
            throw new ModelManagerException('', 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function delete($object)
    {
        try {
            $this->manager->delete($object, $this->deleteFlush);
        } catch (DBALException $e) {
            throw new ModelManagerException('', 0, $e);
        } catch (\PDOException $e) {
            throw new ModelManagerException('', 0, $e);
        }
    }

    /**
     * @param string $class
     * @param ProxyQueryInterface $queryProxy
     *
     * @throws ModelManagerException
     */
    public function batchDelete($class, ProxyQueryInterface $queryProxy)
    {
        $this->deleteFlush = false;
        $queryProxy->select('DISTINCT ' . $queryProxy->getRootAlias());

        $i = 0;
        try {
            foreach ($queryProxy->getQuery()->iterate() as $pos => $object) {
                $this->delete($object[0]);

                if ((++$i % 20) == 0) {
                    $this->getEntityManager($class)->flush();
                    $this->getEntityManager($class)->clear();
                }
            }

            $this->getEntityManager($class)->flush();
            $this->getEntityManager($class)->clear();
        } catch (DBALException $e) {
            throw new ModelManagerException('', 0, $e);
        } catch (\PDOException $e) {
            throw new ModelManagerException('', 0, $e);
        }
    }
}
