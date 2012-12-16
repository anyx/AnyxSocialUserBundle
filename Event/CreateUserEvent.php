<?php

namespace Anyx\SocialUserBundle\Event;

use Symfony\Component\EventDispatcher\Event as BaseEvent;

/**
 * 
 */
class CreateUserEvent extends BaseEvent
{

    /**
     * 
     */
    protected $user;

    /**
     * 
     */
    protected $account;

    /**
     *
     * @param mixed $user
     * @param mixed $$account 
     */
    public function __construct($user, $account)
    {
        $this->user = $user;
        $this->account = $account;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getAccount()
    {
        return $this->account;
    }
}