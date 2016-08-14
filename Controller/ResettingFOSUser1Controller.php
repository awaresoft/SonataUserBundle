<?php

namespace Awaresoft\Sonata\UserBundle\Controller;

use Sonata\UserBundle\Controller\ResettingFOSUser1Controller as BaseResettingFOSUser1Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ResettingFOSUser1Controller.
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class ResettingFOSUser1Controller extends BaseResettingFOSUser1Controller
{
    /**
     * @inheritdoc
     */
    public function resetAction($token)
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $this->container->get('fos_user.user_manager')->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        if (!$user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
            return new RedirectResponse($this->container->get('router')->generate('fos_user_resetting_request'));
        }

        $formFactory = $this->container->get('awaresoft.user.resetting.form.factory');
        $form = $formFactory->createForm();
        $form->setData($user);
        $form->handleRequest($this->container->get('request'));

        if ($form->isValid()) {
            $user->setConfirmationToken(null);
            $user->setPasswordRequestedAt(null);
            $user->setEnabled(true);
            $userManager->updateUser($user);

            $this->setFlash('fos_user_success', 'resetting.flash.success');
            $response = new RedirectResponse($this->getRedirectionUrl($user));
            $this->authenticateUser($user, $response);

            return $response;
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Resetting:reset.html.'.$this->getEngine(), array(
            'token' => $token,
            'form' => $form->createView(),
        ));
    }
}
