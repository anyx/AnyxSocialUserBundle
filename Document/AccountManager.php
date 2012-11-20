<?php

namespace Anyx\SocialUserBundle\Document;

use Anyx\SocialUserBundle\Model\AccountManager as BaseManager;
use Anyx\SocialUserBundle\Util\ValueFinder;

class AccountManager extends BaseManager
{
    protected $userClass = 'Anyx\SocialUserBundle\Model\User';

    /**
     * 
     * @return string
     */
    public function getUserClass()
    {
        return $this->userClass;
    }

    /**
     * 
     * @param string $userClass
     */
    public function setUserClass($userClass)
    {
        $this->userClass = $userClass;
    }

    /**
     *
     * @param string $service
     * @param array $userData
     * @return Anyx\SocialUserBundle\Document\SocialAccount | null 
     */
    public function findAccount($service, array $userData)
    {
        throw new \RuntimeException('Account is property of user');
    }
}
