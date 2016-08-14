<?php

namespace Awaresoft\Sonata\UserBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;
use Symfony\Component\HttpFoundation\Request;

class LoginFailureHandler extends DefaultAuthenticationFailureHandler
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($failureUrl = $request->get($this->options['failure_path_parameter'], null, true)) {
            $this->options['failure_path'] = $failureUrl;
        }

        if (null === $this->options['failure_path']) {
            $this->options['failure_path'] = $this->options['login_path'];
        }

        if ($this->options['failure_forward']) {
            if (null !== $this->logger) {
                $this->logger->debug('Authentication failure, forward triggered.', array('failure_path' => $this->options['failure_path']));
            }

            $subRequest = $this->httpUtils->createRequest($request, $this->options['failure_path']);
            $subRequest->attributes->set(Security::AUTHENTICATION_ERROR, $exception);

            return $this->httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
        }

        if (null !== $this->logger) {
            $this->logger->debug('Authentication failure, redirect triggered.', array('failure_path' => $this->options['failure_path']));
        }

        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);

        return $this->createRedirectResponse($this->options['failure_path']);
    }

    /**
     * Creates a redirect Response.
     *
     * @param string  $path    A path (an absolute path (/foo), an absolute URL (http://...), or a route name (foo))
     * @param int     $status  The status code
     *
     * @return RedirectResponse A RedirectResponse instance
     */
    public function createRedirectResponse($path, $status = 302)
    {
        return new RedirectResponse($this->container->get('router')->generate($path), $status);
    }
}