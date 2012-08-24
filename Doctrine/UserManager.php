<?php

namespace Anyx\SocialUserBundle\Doctrine;


use Anyx\SocialUserBundle\Event;
use Anyx\SocialUserBundle\Model\SocialAccount as BaseSocialAccount;
use FOS\UserBundle\Doctrine\UserManager as BaseUserManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Anyx\SocialUserBundle\Model;

class UserManager extends BaseUserManager
{
    /**
	 *
	 */
	private $dispatcher;
	
	/**
	 *
	 * @return EventDispatcherInterface $dispatcher
	 */
	public function getDispatcher() {
		return $this->dispatcher;
	}

	/**
	 *
	 * @param EventDispatcherInterface $dispatcher 
	 */
	public function setDispatcher( EventDispatcherInterface $dispatcher) {
		$this->dispatcher = $dispatcher;
	}

	/**
	 *
	 * @param BaseSocialAccount $account 
	 */
	public function createUserFromAccount( BaseSocialAccount $account ) {
		$user = $this->createUser();
		
		$user->addSocialAccount( $account );
		$user->setUsername( $account->getServiceName() . $account->getAccountId() . time() );
		
		return $user;
	}
	
	/**
	 *
	 * @param BaseSocialAccount $account
	 * @return User 
	 */
	public function getAccountOwner( BaseSocialAccount $account ) {
		
		$user = $this->findUserByAccount($account);
		if ( empty( $user ) ) {
			$user = $this->createUserFromAccount($account);
		}
		
		return $user;
	}

	/**
	 *
	 * @param SocialAccount $account 
	 */
	public function findUserByAccount( BaseSocialAccount $account ) {
        return $this->findUserBy(array(
            'socialAccounts.accountId'      => (string) $account->getAccountId(),
            'socialAccounts.serviceName'    => $account->getServiceName()
        ));
	}    
    
	/**
	 *
	 * @param Anyx\SocialUserBundle\Model\User $parent
	 * @param Anyx\SocialUserBundle\Model\User $child 
	 */
	public function mergeUsers( Model\User $parent, Model\User $child ) {
		$this->getDispatcher()->dispatch(SocialUserBundle\Events::onMergeUsers, new Event\MergeUsersEvent( $parent, $child) );
	}
}
