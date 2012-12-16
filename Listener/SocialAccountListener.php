<?php

namespace Anyx\SocialUserBundle\Listener;

use Anyx\SocialUserBundle\Model\AccountManager;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class SocialAccountListener
{
    private $accountManager;
   
    /**
     * 
     * @param \Anyx\SocialUserBundle\Model\AccountManager $accountManager
     */
    public function __construct(AccountManager $accountManager)
    {
        $this->accountManager = $accountManager;
    }

    /**
     * 
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $this->accountManager->initAccountsMap();
    }
}

