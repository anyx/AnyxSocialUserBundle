<?php

namespace Anyx\SocialUserBundle\Entity;

use Anyx\SocialUserBundle\Model\AccountManager as BaseManager;
use Anyx\SocialUserBundle\Util\ValueFinder;

class AccountManager extends BaseManager
{
    /**
     *
     * @param string $service
     * @param array $userData
     * @return Anyx\SocialUserBundle\Entity\User | null 
     */
    public function findAccount( $service, array $userData ) {
        return $this->getObjectManager()
                    ->getRepository($this->getAccountClass())
                    ->findOneBy(array(
                        'serviceName'  => $service,
                        'accountId'    => ValueFinder::findFieldValue('accountId', $userData, $this->getAccountMap($service))
                    ));
    }
}
