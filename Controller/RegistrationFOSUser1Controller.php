<?php

namespace Awaresoft\Sonata\UserBundle\Controller;

use Sonata\UserBundle\Controller\RegistrationFOSUser1Controller as BaseRegistrationFOSUser1Controller;
use Sonata\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Extended RegistrationFOSUser1Controller Sonata class.
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class RegistrationFOSUser1Controller extends BaseRegistrationFOSUser1Controller
{
    public function resendConfirmAction(Request $request)
    {
        $formFactory = $this->get('awaresoft.user.resend_activation.form.factory');
        $userManager = $this->get('sonata.user.user_manager');

        $user = $userManager->createUser();
        $form = $formFactory->createForm();
        $form->setData($user);

        if ('POST' === $request->getMethod()) {
            $form->submit($request);

            if ($form->isValid()) {
                $email = $user->getEmail();
                $user = $userManager->findUserByEmail($email);

                if (null === $user) {
                    throw new NotFoundHttpException(sprintf('The user with email "%s" does not exist', $email));
                }

                if (!$url = $this->resendConfirmationEmail($user)) {
                    $url = $this->container->get('router')->generate('fos_user_registration_confirmed');
                }

                $userManager->updateUser($user);

                return new RedirectResponse($url);
            }
        }

        return $this->render('AwaresoftSonataUserBundle:Registration:resend_confirm.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function checkEmailAction()
    {
        $email = $this->get('session')->get('fos_user_send_confirmation_email/email');
        $this->get('session')->remove('fos_user_send_confirmation_email/email');
        $user = $this->get('fos_user.user_manager')->findUserByEmail($email);
        $email = null;

        if (null !== $user) {
            $email = $user->getEmail();
        }

        return $this->render('FOSUserBundle:Registration:checkEmail.html.' . $this->getEngine(), [
            'email' => $email,
        ]);
    }

    /**
     * @param UserInterface $user
     *
     * @return string
     */
    protected function resendConfirmationEmail(UserInterface $user)
    {
        $user->setEnabled(false);
        if (null === $user->getConfirmationToken()) {
            $user->setConfirmationToken($this->get('fos_user.util.token_generator')->generateToken());
        }

        $this->get('fos_user.mailer')->sendConfirmationEmailMessage($user);
        $this->get('session')->set('fos_user_send_confirmation_email/email', $user->getEmail());

        return $this->get('router')->generate('fos_user_registration_check_email');
    }
}
