<?php

namespace Anyx\SocialUserBundle\Entity;

use Anyx\SocialUserBundle\Doctrine\UserManager as BaseManager;
use Anyx\SocialUserBundle\Model\SocialAccount as BaseSocialAccount;

/**
 * 
 */
class UserManager extends BaseManager
{
	/**
	 *
	 * @param SocialAccount $account 
	 */
	public function findUserByAccount( BaseSocialAccount $account ) {
        return $account->getOwner();
	}    
}
