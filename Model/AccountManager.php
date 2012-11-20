<?php

namespace Anyx\SocialUserBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Anyx\SocialUserBundle\Model\SocialAccount;

/**
 *
 */
abstract class AccountManager
{

    /**
     * @var 
     */
    protected $objectManager;

    /**
     * @var string 
     */
    protected $accountClass = 'Anyx\SocialUserBundle\Model\SocialAccount';

    /**
     * @var array
     */
    protected $accountsMap;

    /**
     * @param string $service
     * @param array $userData
     */
    abstract public function findAccount($service, array $userData);

    /**
     *
     * @param array $accountsMap 
     */
    function __construct(ObjectManager $objectManager, array $accountsMap)
    {
        $this->objectManager = $objectManager;
        $this->accountsMap = $accountsMap;
        SocialAccount::setCommonDataMap($accountsMap);
    }

    /**
     *
     * @return Doctrine\Common\Persistence\ObjectManager
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }

    /**
     *
     * @return Anyx\SocialUserBundle\Model\SocialAccount
     */
    public function getAccountClass()
    {
        return $this->accountClass;
    }

    /**
     *
     * @param string $accountClass 
     */
    public function setAccountClass($accountClass)
    {
        $this->accountClass = $accountClass;
    }

    /**
     *
     * @param string $service
     * @param array $userData 
     */
    public function getAccount($service, array $userData)
    {
        $account = $this->findAccount($service, $userData);

        if (empty($account)) {
            $account = $this->createAccount($service, $userData);
        }

        return $account;
    }

    /**
     *
     * @param string $service
     * @param array $userData 
     */
    public function createAccount($service, $userData)
    {
        $class = $this->getAccountClass();
        $account = new $class;

        $account->setServiceName($service);
        $account->setData($userData);

        return $account;
    }

    /**
     *
     * @param string $service
     * @return array
     */
    protected function getAccountMap($service)
    {
        if (!array_key_exists($service, $this->accountsMap)) {
            throw new \InvalidArgumentException("Account fields map not found for service '$service' ");
        }
        return $this->accountsMap[$service];
    }
}
