<?php

namespace Anyx\SocialUserBundle\Doctrine;

use Anyx\SocialUserBundle\Event;
use Anyx\SocialUserBundle\Model\SocialAccount as BaseSocialAccount;
use FOS\UserBundle\Doctrine\UserManager as BaseUserManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Anyx\SocialUserBundle\Model;

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

        return $user;
    }

    /**
     *
     * @param BaseSocialAccount $account
     * @return User 
     */
    public function getAccountOwner(BaseSocialAccount $account)
    {
        if ($account->getId()) {
            $user = $this->findUserByAccount($account);
        } else {
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
        $this->getDispatcher()->dispatch(SocialUserBundle\Events::onMergeUsers, new Event\MergeUsersEvent($parent, $child));
    }
}