<?php

namespace Anyx\SocialUserBundle\Document;

use Anyx\SocialUserBundle\Model\SocialAccount;
use FOS\UserBundle\Document\User as BaseUser;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * 
 * @MongoDB\Document
 */
class User extends BaseUser
{

    /**
     * @MongoDB\Id(strategy="auto")
     */
    protected $id;

    /**
     * @MongoDB\EmbedMany(targetDocument="Anyx\SocialUserBundle\Document\SocialAccount")
     */
    protected $socialAccounts;

    /**
     * @MongoDB\Date
     */
    protected $createdAt;

    /**
     * @MongoDB\Date
     */
    protected $updatedAt;

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

    /**
     * 
     * @return boolean
     */
    public function hasSocialAccounts()
    {
        return !empty($this->socialAccounts);
    }

    /**
     *
     * @param string $service 
     */
    public function hasSocialAccount($service)
    {
        return $this->getSocialAccount($service) != null;
    }

    /**
     *
     * @param string $service 
     */
    public function getSocialAccount($service)
    {
        if ($this->hasSocialAccounts()) {
            foreach ($this->getSocialAccounts() as $account) {
                if ($account->getServiceName() == $service) {
                    return $account;
                }
            }
        }

        return null;
    }
    
    /**
     * @MongoDB\PreUpdate
     */
    public function setUpdatedAt()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * @MongoDB\PrePersist
     */
    public function setCreatedAt()
    {
        $this->createdAt = $this->updatedAt = new \DateTime();
    }

    /**
     *
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     *
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}