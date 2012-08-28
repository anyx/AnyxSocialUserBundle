<?php

namespace Anyx\SocialUserBundle\Entity;

use Anyx\SocialUserBundle\Model\User as BaseUser;

use Anyx\SocialUserBundle\Model\SocialAccount;

class User extends BaseUser
{
    public function addSocialAccount( SocialAccount $account )
    {
        if( parent::addSocialAccount($account) ) {
            $account->setOwner($this);
        }
    }
}