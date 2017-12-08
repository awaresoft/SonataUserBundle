<?php

namespace Awaresoft\Sonata\UserBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\UserBundle\Admin\Entity\UserAdmin as BaseUserAdmin;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Extended class Sonata UserAdmin
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class UserAdmin extends BaseUserAdmin
{
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
            ->add('plainPassword', 'text', array(
                'required' => (!$this->getSubject() || is_null($this->getSubject()->getId())),
            ))
            ->end()
            ->with('Profile')
            ->add('firstname', null, array('required' => false))
            ->add('lastname', null, array('required' => false))
            ->add('phone', null, array('required' => false))
            ->add('gender', 'sonata_user_gender', array(
                'required' => true,
                'translation_domain' => $this->getTranslationDomain(),
            ))
            ->add('locale', 'locale', array('required' => false))
            ->add('timezone', 'timezone', array('required' => false))
            ->add('dateOfBirth', 'sonata_type_date_picker', array(
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
                ->add('groups', 'sonata_type_model', array(
                    'required' => false,
                    'expanded' => true,
                    'multiple' => true,
                ))
                ->end()
                ->with('Roles')
                ->add('realRoles', 'sonata_security_roles', array(
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
                $user = $this->getConfigurationPool()
                    ->getContainer()
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
