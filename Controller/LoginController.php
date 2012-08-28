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
	 * @var Anyx\SocialUserBundle\User\AccountFactory;
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
	 *
	 * @param Authentication\Manager $authenticationManager
	 * @param UserManager $userManager
	 * @param type $securityContext 
	 */
	function __construct(	Authentication\Manager $authenticationManager,
							AccountManager $accountFactory,
							UserManager $userManager,
							SecurityContext $securityContext ) {
		
		$this->authenticationManager = $authenticationManager;
		$this->userManager = $userManager;
		$this->accountManager = $accountFactory;
		$this->securityContext = $securityContext;
	}

	/**
	 *
	 * @return Anyx\SocialBundle\Authentication\Manager
	 */
	public function getAuthenticationManager() {
		return $this->authenticationManager;
	}

	/**
	 *
	 * @return Symfony\Component\Security\Core\SecurityContext
	 */
	public function getSecurityContext() {
		return $this->securityContext;
	}

	/**
	 *
	 * @return Anyx\SocialUserBundle\User\AccountFactory
	 */
	public function getAccountManager() {
		return $this->accountManager;
	}

	/**
	 *
	 * @return UserManager
	 */
	public function getUserManager() {
		return $this->userManager;
	}

    /**
     * @Route("/auth/{service}",  name="anyx_social_auth")
     */
	public function authAction( $service, Request $request ) {
		
		$manager = $this->getAuthenticationManager();

		$manager->getProviderFactory()->setProvidersOption( 'redirect_uri', $this->getRedirectUri( $request ) );
		
		$accessToken = $manager->getAccessToken( $service, $request );

		$userData = $manager->getProviderFactory()->getProvider( $service )->getUserData( $accessToken );
		
		$account = $this->getAccountManager()->getAccount( $service, $userData );
		
		$userManager = $this->getUserManager();
		
		if ( $this->getSecurityContext()->isGranted('ROLE_USER') ) {
	
			$accountOwner = $userManager->findUserByAccount( $account );
			
			//link account
			$currentUser = $this->getSecurityContext()->getToken()->getUser();
			$currentUser->addSocialAccount( $account );
			
			if ( !empty( $accountOwner ) ) {
				$userManager->mergeUsers( $currentUser, $accountOwner );
			}

		} else {
			
			$accountOwner = $userManager->getAccountOwner( $account );
			
			$token = new UsernamePasswordToken(
					$accountOwner,
					null,
					'main',//todo!!!
					$accountOwner->getRoles()
			);
			$this->getSecurityContext()->setToken($token);
			
			$currentUser = $accountOwner;
		}
		
        if ( null == $currentUser->getEmail() ) {
            $currentUser->setEmail('noemail');
        }

		$userManager->updateUser( $currentUser );
		
		$backurl = $request->get( 'backurl' );
		if ( empty( $backurl ) ) {
			$backurl = '/';
		}
		
		return new RedirectResponse( $request->getBaseUrl() . $backurl );
	}
	
	/**
	 * @todo refacator this shit
	 * 
	 * @return string
	 */
	protected function getRedirectUri( Request $request ) {

		$path = $request->getUri();
		$path = str_replace( $request->getQueryString(), '', $request->getUri() );
		if (strpos($path, '?') == strlen($path) - 1 ) {
			$path = substr($path, 0, -1);
		}
		
		return $path;
	}
}