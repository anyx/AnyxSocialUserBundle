<?php

namespace Anyx\SocialUserBundle\Doctrine;

use Anyx\SocialUserBundle\Event;
use Anyx\SocialUserBundle\Model\SocialAccount as BaseSocialAccount;
use FOS\UserBundle\Doctrine\UserManager as BaseUserManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Anyx\SocialUserBundle\Model;
use Anyx\SocialUserBundle\Events;

abstract class UserManager extends BaseUserManager
{
    /**
     *
     */
    private $dispatcher;

    /**
     *
     * @param SocialAccount $account 
     */
    abstract public function findUserByAccount(BaseSocialAccount $account);

    /**
     *
     * @return EventDispatcherInterface $dispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     *
     * @param EventDispatcherInterface $dispatcher 
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     *
     * @param BaseSocialAccount $account 
     */
    public function createUserFromAccount(BaseSocialAccount $account)
    {
        $user = $this->createUser();

        $email = $account->getValue('email');

        $user->addSocialAccount($account);
        $user
            ->setUsername($account->getServiceName() . $account->getAccountId() . time())
            ->setPassword($account->getServiceName() . $account->getAccountId() . time())
            ->setEmail($email)
            ->setEmailCanonical($email)
        ;

        $event = $this->getDispatcher()->dispatch(
                Events::onCreateUser, new Event\CreateUserEvent($user, $account)
        );
        
        return $event->getUser();
    }

    /**
     *
     * @param BaseSocialAccount $account
     * @return User 
     */
    public function getAccountOwner(BaseSocialAccount $account)
    {
        $user = $this->findUserByAccount($account);
        
        if (empty($user)) {
            $user = $this->createUserFromAccount($account);
        }

        return $user;
    }

    /**
     *
     * @param Anyx\SocialUserBundle\Model\User $parent
     * @param Anyx\SocialUserBundle\Model\User $child 
     */
    public function mergeUsers(Model\User $parent, Model\User $child)
    {
        $this->getDispatcher()->dispatch(Events::onMergeUsers, new Event\MergeUsersEvent($parent, $child));
    }
}