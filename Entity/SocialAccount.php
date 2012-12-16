<?php

namespace Anyx\SocialUserBundle\Entity;

use Anyx\SocialUserBundle\Model\SocialAccount as BaseSocialAccount;
use Anyx\SocialUserBundle\Entity\User;

class SocialAccount extends BaseSocialAccount
{
    /**
     * @var Anyx\SocialUserBundle\Entity\User 
     */
    protected $owner;
    
    /**
     * @return Anyx\SocialUserBundle\Entity\User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param Anyx\SocialUserBundle\Entity\User $owner 
     */
    public function setOwner( User $owner)
    {
        $this->owner = $owner;
    }

    /**
     * 
     */
    public function serializeData() {
        $this->data = json_encode($this->data);
    }

    /**
     * 
     */
    public function deserializeData() {
        $this->data = json_decode($this->data, true);
    }
}
