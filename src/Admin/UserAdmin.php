<?php

namespace Awaresoft\Sonata\UserBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\Filter\DateType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\UserBundle\Admin\Entity\UserAdmin as BaseUserAdmin;
use Sonata\UserBundle\Form\Type\SecurityRolesType;
use Sonata\UserBundle\Form\Type\UserGenderListType;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Extended class Sonata UserAdmin
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class UserAdmin extends BaseUserAdmin implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        // define group zoning
        $formMapper
            ->tab('User')
            ->with('General', array('class' => 'col-md-6'))->end()
            ->with('Profile', array('class' => 'col-md-6'))->end()
            ->end();

        if ($this->isGranted('MASTER')) {
            $formMapper
                ->tab('Security')
                ->with('Status', array('class' => 'col-md-4'))->end()
                ->with('Groups', array('class' => 'col-md-4'))->end()
                ->with('Roles', array('class' => 'col-md-12'))->end()
                ->end();
        }

        $now = new \DateTime();

        $formMapper
            ->tab('User')
            ->with('General')
            ->add('username')
            ->add('email')
            ->add('plainPassword', TextType::class, array(
                'required' => (!$this->getSubject() || is_null($this->getSubject()->getId())),
            ))
            ->end()
            ->with('Profile')
            ->add('firstname', null, array('required' => false))
            ->add('lastname', null, array('required' => false))
            ->add('phone', null, array('required' => false))
            ->add('gender', UserGenderListType::class, array(
                'required' => true,
                'translation_domain' => $this->getTranslationDomain(),
            ))
            ->add('locale', LocaleType::class, array('required' => false))
            ->add('timezone', TimezoneType::class, array('required' => false))
            ->add('dateOfBirth', DateType::class, array(
                'years' => range(1900, $now->format('Y')),
                'dp_min_date' => '1-1-1900',
                'dp_max_date' => $now->format('c'),
                'required' => false,
            ))
            ->end()
            ->with('Social')
            ->add('facebookUid', null, array('required' => false))
            ->add('facebookName', null, array('required' => false))
            ->add('twitterUid', null, array('required' => false))
            ->add('twitterName', null, array('required' => false))
            ->add('gplusUid', null, array('required' => false))
            ->add('gplusName', null, array('required' => false))
            ->end()
            ->end();

        if ($this->isGranted('OPERATOR')) {
            $formMapper
                ->tab('Security')
                ->with('Status')
                ->add('enabled', CheckboxType::class, array('required' => false))
                ->end()
                ->with('Groups')
                ->add('groups', ModelType::class, array(
                    'required' => false,
                    'expanded' => true,
                    'multiple' => true,
                ))
                ->end()
                ->with('Roles')
                ->add('realRoles', SecurityRolesType::class, array(
                    'label' => 'form.label_roles',
                    'expanded' => true,
                    'multiple' => true,
                    'required' => false,
                ))
                ->end()
                ->end();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function checkAccess($action, $object = null)
    {
        try {
            parent::checkAccess($action, $object);
        } catch (AccessDeniedException $ex) {
            if ($action == 'edit') {
                $user = $this->container
                    ->get('security.token_storage')
                    ->getToken()
                    ->getUser();

                if ($object->getId() === $user->getId()) {
                    return;
                }

                throw new AccessDeniedException(sprintf(
                    'Access Denied to edit user: %d profile by user: %d',
                    $object->getId(),
                    $user->getId()
                ));
            }
        }
    }
}
