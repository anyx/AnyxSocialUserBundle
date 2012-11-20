<?php

namespace Anyx\SocialUserBundle\Model;

use Anyx\SocialUserBundle\Model\SocialAccount;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * 
 */
class User extends BaseUser
{
    /**
     *
     */
    protected $socialAccounts;

    /**
     * Свойство для сохранения сервиса, из которого авторизовался пользователь
     * @var string
     */
    protected $authenticatedFrom = null;
    
    /**
     *
     */
    public function getSocialAccounts()
    {
        return $this->socialAccounts;
    }

    /**
     *
     */
    public function setSocialAccounts($socialAccounts)
    {
        $this->socialAccounts = $socialAccounts;
    }

    /**
     *
     * @param SocialAccount $account 
     */
    public function addSocialAccount(SocialAccount $account)
    {

        if (!empty($this->socialAccounts)) {
            foreach ($this->socialAccounts as $existAccount) {
                if ($existAccount->getServiceName() == $account->getServiceName()) {
                    return false;
                }
            }
        }

        $this->socialAccounts[] = $account;

        return true;
    }
    
    public function getAuthenticatedFrom()
    {
        return $this->authenticatedFrom;
    }

    public function setAuthenticatedFrom($authenticatedFrom)
    {
        $this->authenticatedFrom = $authenticatedFrom;
    }
}