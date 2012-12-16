<?php

namespace Anyx\SocialUserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Anyx\SocialBundle\Authentication;
use Anyx\SocialUserBundle\Model\AccountManager;
use Anyx\SocialUserBundle\Doctrine\UserManager as UserManager;

/**
 * @Route("",service="anyx_social_user.controller.login") 
 */
class LoginController extends Controller
{
    /**
     * @var Anyx\SocialBundle\Authentication\Manager
     */
    protected $authenticationManager;

    /**
     * @var Anyx\SocialUserBundle\User\AccountManager;
     */
    protected $accountManager;

    /**
     * @var FOS\UserBundle\Document\UserManager
     */
    protected $userManager;

    /**
     * @var Symfony\Component\Security\Core\SecurityContext
     */
    protected $securityContext;

    /**
     * @var string
     */
    protected $firewallName;

    /**
     *
     * @param Authentication\Manager $authenticationManager
     * @param AccountManager $accountManager,
     * @param UserManager $userManager
     * @param type $securityContext 
     */
    public function __construct(Authentication\Manager $authenticationManager, AccountManager $accountManager, UserManager $userManager, SecurityContext $securityContext, $firewallName)
    {
        $this->authenticationManager = $authenticationManager;
        $this->userManager = $userManager;
        $this->accountManager = $accountManager;
        $this->securityContext = $securityContext;
        $this->firewallName = $firewallName;
    }

    public function getFirewallName()
    {
        return $this->firewallName;
    }

    /**
     *
     * @return Anyx\SocialBundle\Authentication\Manager
     */
    public function getAuthenticationManager()
    {
        return $this->authenticationManager;
    }

    /**
     *
     * @return Symfony\Component\Security\Core\SecurityContext
     */
    public function getSecurityContext()
    {
        return $this->securityContext;
    }

    /**
     *
     * @return Anyx\SocialUserBundle\User\AccountFactory
     */
    public function getAccountManager()
    {
        return $this->accountManager;
    }

    /**
     *
     * @return UserManager
     */
    public function getUserManager()
    {
        return $this->userManager;
    }

    /**
     * @Route("/auth/{service}",  name="anyx_social_auth")
     */
    public function authAction($service, Request $request)
    {
        $manager = $this->getAuthenticationManager();
        $manager->getProviderFactory()->setProvidersOption('redirect_uri', $this->getRedirectUri($request));

        $accessToken = $manager->getAccessToken($service, $request);

        $userData = $manager->getProviderFactory()->getProvider($service)->getUserData($accessToken);

        $account = $this->getAccountManager()->createAccount($service, $userData);

        $userManager = $this->getUserManager();

        if ($this->getSecurityContext()->isGranted('ROLE_USER')) {

            $accountOwner = $userManager->findUserByAccount($account);

            //link account
            $currentUser = $this->getSecurityContext()->getToken()->getUser();
            $currentUser->addSocialAccount($account);

            if (!empty($accountOwner)) {
                $userManager->mergeUsers($currentUser, $accountOwner);
            }
        } else {

            $accountOwner = $userManager->getAccountOwner($account);

            $token = new UsernamePasswordToken(
                            $accountOwner,
                            null,
                            $this->getFirewallName(),
                            $accountOwner->getRoles()
            );
            $this->getSecurityContext()->setToken($token);

            $currentUser = $accountOwner;
        }

        $userManager->updateUser($currentUser);

        $backurl = $request->get('backurl');
        if (empty($backurl)) {
            $backurl = '/';
        }

        return new RedirectResponse($request->getBaseUrl() . $backurl);
    }

    /**
     * @return string
     */
    protected function getRedirectUri(Request $request)
    {
        $path = str_replace($request->getQueryString(), '', $request->getUri());
        if (strpos($path, '?') == strlen($path) - 1) {
            $path = substr($path, 0, -1);
        }

        return $path;
    }

    /**
     *
     * @param \FOS\UserBundle\Model\UserInterface $user
     */
    protected function authenticateUser(UserInterface $user, $socialService)
    {
        try {

            $user->setAuthenticatedFrom($socialService);

            $this->container->get('fos_user.security.login_manager')->loginUser(
                    $this->container->getParameter('fos_user.firewall_name'), $user
            );
        } catch (\Exception $ex) {
            $this->get('request')->getSession()->setFlash('fos_user_error', $ex->getMessage());
        }
    }

}