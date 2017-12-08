<?php

namespace Awaresoft\Sonata\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * AwaresoftSonataUserBundle class
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class AwaresoftSonataUserBundle extends Bundle
{
    /**
     * @var null|string
     */
    protected $parent;

    /**
     * @param string $parent
     */
    public function __construct($parent = null)
    {
        $this->parent = $parent;
    }
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->parent;
    }
}
