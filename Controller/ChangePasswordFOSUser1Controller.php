<?php

namespace Awaresoft\Sonata\UserBundle\Controller;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Extended ChangePasswordFOSUser1Controller class.
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class ChangePasswordFOSUser1Controller extends Controller
{
    /**
     * @return Response|RedirectResponse
     *
     * @throws AccessDeniedException
     */
    public function changePasswordAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            $this->createAccessDeniedException('This user does not have access to this section.');
        }

        $formFactory = $this->get('awaresoft.user.change_password.form.factory');
        $form = $formFactory->createForm();
        $form->setData($user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->setFlash('fos_user_success', 'change_password.flash.success');
            $userManager = $this->get('fos_user.user_manager');
            $userManager->updateUser($user);
            return new RedirectResponse($this->getRedirectionUrl($user));
        }

        return $this->render(
            'SonataUserBundle:ChangePassword:changePassword.html.' . $this->container->getParameter('fos_user.template.engine'),
            ['form' => $form->createView()]
        );
    }

    /**
     * @param UserInterface $user
     *
     * @return string
     */
    protected function getRedirectionUrl(UserInterface $user)
    {
        return $this->generateUrl('sonata_user_profile_show');
    }

    /**
     * @param string $action
     * @param string $value
     */
    protected function setFlash($action, $value)
    {
        $this->get('session')->getFlashBag()->set($action, $value);
    }
}
