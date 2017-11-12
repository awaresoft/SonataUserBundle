<?php

namespace Awaresoft\Sonata\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;


/**
 * AwaresoftSonataTimelineBundle class
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class AwaresoftSonataUserBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'SonataUserBundle';
    }
}
