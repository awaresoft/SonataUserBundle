<?php

namespace Awaresoft\Sonata\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ResendConfirmFormType
 *
 * Based on v2 version of fos user added not exists resend confirmation token functionality
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class ResendConfirmFormType extends AbstractType
{
    private $class;

    public function __construct($class)
    {
        $this->class = $class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', 'email', array('label' => 'Email'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class,
            'intention'  => 'resend_confirm',
        ));
    }

    public function getName()
    {
        return 'user_resend_confirm';
    }
}