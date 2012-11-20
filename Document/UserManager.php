<?php

namespace Anyx\SocialUserBundle\Document;

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
    public function findUserByAccount(BaseSocialAccount $account)
    {
        return $this->objectManager
                    ->getRepository($this->getClass())
                    ->findOneBy(array(
                        'accounts.$id' => $account->getAccountId(),
                    ));
    }
}
