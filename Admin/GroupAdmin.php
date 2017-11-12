<?php

namespace Awaresoft\Sonata\UserBundle\Admin;

use Sonata\UserBundle\Admin\Model\GroupAdmin as BaseGroupAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;

/**
 * Extended Class Sonata GroupAdmin
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class GroupAdmin extends BaseGroupAdmin
{
    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name');
    }
}
