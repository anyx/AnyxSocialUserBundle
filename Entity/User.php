<?php

namespace Anyx\SocialUserBundle\Entity;

use Anyx\SocialUserBundle\Model\User as BaseUser;

use Anyx\SocialUserBundle\Model\SocialAccount as BaseSocialAccount;

class User extends BaseUser
{
    public function addSocialAccount( BaseSocialAccount $account )
    {
        if( parent::addSocialAccount($account) ) {
            $account->setOwner($this);
        }
    }
}