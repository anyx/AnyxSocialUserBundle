<?php

namespace Anyx\SocialUserBundle\Document;


use Anyx\SocialUserBundle;
use Anyx\SocialUserBundle\Event;
use Anyx\SocialUserBundle\Model\SocialAccount as BaseSocialAccount;
use FOS\UserBundle\Document\UserManager as BaseUserManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Description of UserManager
 *
 * @author aleks
 */
class UserManager extends BaseUserManager {
	
	/**
	 *
	 */
	private $dispatcher;
	

	/**
	 *
	 * @return EventDispatcherInterface $dispatcher
	 */
	public function getDispatcher(  ) {
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
	 * @param SocialAccount $account 
	 */
	public function findUserByAccount( BaseSocialAccount $account ) {
		return $this->repository
				->createQueryBuilder()
				//->field('enabled')->equals( true )
				->field('socialAccounts.accountId')->equals( (string) $account->getAccountId() )
				->field('socialAccounts.serviceName')->equals( $account->getServiceName() )
				->getQuery()
				->getSingleResult(); 
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
	 * @param Anyx\SocialUserBundle\Document\User $parent
	 * @param Anyx\SocialUserBundle\Document\User $child 
	 */
	public function mergeUsers( User $parent, User $child ) {
		$this->getDispatcher()->dispatch(SocialUserBundle\Events::onMergeUsers, new Event\MergeUsersEvent( $parent, $child) );
	}
}
